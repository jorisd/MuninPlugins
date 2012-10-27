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
                echo "url".$name.".value 0\n";
            }
            else {
                echo "url".$name.".value $time\n";
            }
        }
        else {
            echo "url".$name.".value ".strlen($c)."\n";
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
            echo "url".$name.".label ".get_friendly_name($url)."\n";
            echo "url".$name.".critical $value:$value\n";
            echo "url".$name.".info Size in Bytes of the requested service, 0 if not reachable.\n";
        }
        else if ($type=='sizegt') {
            echo "url".$name.".label ".get_friendly_name($url)."\n";
            echo "url".$name.".critical $value:\n";
            echo "url".$name.".info Size in Bytes of the requested service, 0 if not reachable.\n";
        }
        else if ($type=='sizelt') {
            echo "url".$name.".label ".get_friendly_name($url)."\n";
            echo "url".$name.".critical 0:$value\n";
            echo "url".$name.".info Size in Bytes of the requested service, 0 if not reachable.\n";
        }
        else {
            echo "url".$name.".label ".get_friendly_name($url)."\n";
            echo "url".($warning===false) ? '' : "url".$name.".warning 1:$warning\n";
            echo "url".$name.".critical 1:\n";
            echo "url".$name.".info Time in milliseconds to get the requested service, 0 if not reachable.\n";
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

