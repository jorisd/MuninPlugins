#!/usr/bin/php
<?php
/**
 * MuninPlugins
 *
 * Copyright © 2009-2012 Raphael Barbate (potsky) <potsky@me.com> [http://www.potsky.com]
 *
 * This file is part of MuninPlugins.
 *
 * MuninPlugins is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License.
 *
 * MuninPlugins is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MuninPlugins.  If not, see <http://www.gnu.org/licenses/>.
 */

#%# family=auto
#%# capabilities=autoconf

$title   = @getenv('title');
$url     = @getenv('url');
$port    = @getenv('port');
$timeout = @(int)getenv('timeout');
$warning = @getenv('warning');

$title   = ($title=='') ? array_pop(preg_split('/_/',$argv[0])) : $title;
$timeout = ($timeout==0) ? 5 : $timeout;
$warning = ($warning=='no') ? false : ceil(($timeout*2/3)*1000);

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
    echo ($warning===false) ? '' : "ping.warning 1:$warning\n";
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
