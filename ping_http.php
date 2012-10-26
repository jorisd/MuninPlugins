#!/usr/bin/php
<?php
#%# family=auto
#%# capabilities=autoconf
/**
 * Plugin to monitor websites, based on a keyword or size
 *
 * 
 * Munin parameters
 * -----------
 * 
 * ### env.url (mandatory)
 * 
 * The url to ping in http or https.  
 * **eg**: *https://blog.potsky.com*
 * 
 * ### env.type (mandatory)
 * 
 * Several values are available :
 * 
 * * **match** : to match a specific word in the web page  
 * The returned value is the number of milliseconds to download the page or 0 if not available  
 * A critical alarm is launched if value is not equal to *env.value*
 * 
 * * **size** : to test the web page size in bytes  
 * The returned value  is the number of bytes of the page 0 if not available  
 * A critical alarm is launched if value is not equal to *env.value*
 * 
 * * **sizegt** : to test if the web page size is greater than a value in bytes  
 * The returned value  is the number of bytes of the page 0 if not available  
 * A critical alarm is launched if value is not greater than *env.value*
 * 
 * * **sizelt** : to test if the web page size is lower than a value in bytes  
 * The returned value  is the number of bytes of the page 0 if not available  
 * A critical alarm is launched if value is not lower than *env.value*
 * 
 * ### env.value (mandatory)
 * 
 * The value corresponding to the test.
 * 
 * * **eg if type is _match_** : *Potsky*
 * * **eg if type is _size_** :  *17324*
 * * **eg if type is _sizegt_** : *100*
 * * **eg if type is _sizelt_** : *20000*
 * 
 * ### env.title
 * 
 * The title of the graph.  
 * **default**: the right part next to character _ of the plugin name.  
 * **eg**: *Potsky Blog*
 * 
 * ### env.timeout
 * 
 * The timeout of the connexion in seconds.  
 * A warning is launch when the the connection duration is greater than `timeout*2/3`.  
 * **default**: 5  
 * **eg**: *10*
 * 
 * ### env.warning
 * 
 * Enable or disable the warning alert.  
 * If set to *no*, no warning alert will be triggered.  
 * **default**: yes  
 * **eg**: *no*
 * 
 * Find more information on http://blog.potsky.com/yum-plugin-to-ping-http-websites/
 *
 * @author    Potsky
 * @copyright 2012
 * @version   1.0.0
 * @licence   GPLv2
 */

$types   = array("match","size","sizegt","sizelt");

$title   = @getenv('title');
$url     = @getenv('url');
$type    = @getenv('type');
$value   = @getenv('value');
$timeout = @(int)getenv('timeout');
$warning = @getenv('warning');

$title   = ($title=='') ? array_pop(preg_split('/_/',$argv[0])) : $title;
$timeout = ($timeout==0) ? 5 : $timeout;
$warning = ($warning=='no') ? false : ceil(($timeout*2/3)*1000);

function report() {
    global $title,$url,$type,$value,$timeout;

    $urls = explode(' ',$url);
    foreach ($urls as $name=>$url) {

        $timestart = microtime(true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $c = curl_exec($ch);
        curl_close($ch);
        $time      = ceil((microtime(true)-$timestart)*1000);

        if ($type=='match') {
            if (strpos($c,$value)===false) {
                echo $name.".value 0\n";
            }
            else {
                echo $name.".value $time\n";
            }
        }
        else {
            echo $name.".value ".strlen($c)."\n";
        }
    }
}


function autoconf() {
    global $title,$url,$type,$value;

    if (!in_array($type,$types)) {
        echo "no env.type must be one of these values : ".implode(",",$types)."\n";
        die();
    }

    if ($url=='') {
        echo "no env.url must be a valid target url\n";
        die();
    }

    if ($value=='') {
        echo "no env.value is empty !\n";
        die();
    }

    echo "yes\n";
}

function get_friendly_name($url) {
   return (preg_match('@(https{0,1}://[^/]+)@i', $url,$matches)) ? $matches[1] : $url;
}

function config() {
    global $title,$url,$type,$value,$warning;
    echo "graph_title HTTP $type ping for $title\n";
    echo "graph_info The ping graph for an address shows if the service is accessible or not.\n";
    echo "graph_category services\n";
    echo "graph yes\n";

    if ($type=='size') {
        echo "graph_vlabel Bytes\n";
    }
    else if ($type=='sizegt') {
        echo "graph_vlabel Bytes\n";
    }
    else if ($type=='sizelt') {
        echo "graph_vlabel Bytes\n";
    }
    else {
        echo "graph_vlabel ms\n";
    }

    $urls = explode(' ',$url);
    foreach ($urls as $name=>$url) {

        if ($type=='size') {
            echo $name.".label ".get_friendly_name($url)."\n";
            echo $name.".critical $value:$value\n";
            echo $name.".info Size in Bytes of the requested service, 0 if not reachable.\n";
        }
        else if ($type=='sizegt') {
            echo $name.".label ".get_friendly_name($url)."\n";
            echo $name.".critical $value:\n";
            echo $name.".info Size in Bytes of the requested service, 0 if not reachable.\n";
        }
        else if ($type=='sizelt') {
            echo $name.".label ".get_friendly_name($url)."\n";
            echo $name.".critical 0:$value\n";
            echo $name.".info Size in Bytes of the requested service, 0 if not reachable.\n";
        }
        else {
            echo $name.".label ".get_friendly_name($url)."\n";
            echo ($warning===false) ? '' : $name.".warning 1:$warning\n";
            echo $name.".critical 1:\n";
            echo $name.".info Time in milliseconds to get the requested service, 0 if not reachable.\n";
        }
    }
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

