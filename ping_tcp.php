#!/usr/bin/php
<?php
#%# family=auto
#%# capabilities=autoconf
/**
 * Plugin to monitor services reachable via TCP
 *
 * Munin parameters
 * -----------
 * 
 * ### env.url (mandatory)
 * 
 * The url to ping with a TCP socket.  
 * **eg**: *blog.potsky.com*
 * 
 * ### env.port (mandatory)
 * 
 * The port of the TCP socket.  
 * **eg**: *22*
 * 
 * ### env.title
 * 
 * The title of the graph.  
 * **default**: the right part next to character _ of the plugin name.  
 * **eg**: *Potsky Blog SSH*
 * 
 * ### env.timeout
 * 
 * The timeout of the connexion in seconds.  
 * A warning is launch when the the connection duration is greater than `timeout*2/3`.  
 * **default**: 5  
 * **eg**: *10*
 * 
 *
 * Find more information on http://blog.potsky.com/yum-plugin-to-ping-tcp-ports/
 *
 * @author    Potsky
 * @copyright 2012
 * @version   1.0.0
 * @licence   GPLv2
 */

$title   = @getenv('title');
$url     = @getenv('url');
$port    = @getenv('port');
$timeout = @(int)getenv('timeout');

$title   = ($title=='') ? array_pop(preg_split('/_/',$argv[0])) : $title;
$timeout = ($timeout==0) ? 5 : $timeout;
$warning = ceil(($timeout*2/3)*1000);

function report() {
    global $title,$url,$port,$timeout;

    $timestart = microtime(true);
    $fp        = @fsockopen($url,$port,$errno,$errstr,$timeout);
    $time      = ceil((microtime(true)-$timestart)*1000);

    if (!$fp) {
        echo "ping.value 0\n";
    }

    else {
        echo "ping.value $time\n";
        fclose($fp);
    }
}


function autoconf() {
    global $title,$url,$port;

    if ($url=='') {
        echo "no env.url must be a valid target url\n";
        die();
    }

    if ($port=='') {
        echo "no env.port is empty !\n";
        die();
    }

    echo "yes\n";
}

function config() {
    global $title,$url,$port,$warning;
    echo "graph_title TCP port $port ping for $title\n";
    echo "graph_category services\n";
    echo "graph yes\n";
    echo "graph_info The ping graph for an address shows if the service is accessible or not.\n";
    echo "graph_vlabel ms\n";
    echo "ping.label ping\n";
    echo "ping.warning 1:$warning\n";
    echo "ping.critical 1:\n";
    echo "ping.info Time in milliseconds to ping the requested service, 0 if not reachable.\n";
}

if (!isset($argv[1])) {
    report();
}
else  {
    if (function_exists($argv[1])) {
        eval($argv[1].'();');
    }
    else {
        echo 'Unknown argument '.$argv[1]."\n";
    }
}
