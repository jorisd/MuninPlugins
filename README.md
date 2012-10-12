MuninPlugins
============

* ping_http : Plugin to monitor websites, based on a keyword or size
* ping_tcp : Plugin to monitor services reachable via TCP

Requirements
-----------

You need :

* munin and munin-node of course
* PHP in command line


Installation
-----------

* Install these plugins in the master munin server.
* Enable `munin-node` on the master server.
* Copy wanted plugins in `/usr/share/munin/plugins` or anywhere else.


Usage
-----

Link a plugin for a special resource :

    $ ln -s /usr/share/munin/plugins/ping_http_ /etc/munin/plugins/ping_http_www.google.com

Then configure this resource :

    $ cat >> /etc/munin/plugin-conf.d << EOF
    [ping_http_www.google.com]
    user root
    env.title Google
    env.url http://www.google.com
    env.type match
    env.value google
    EOF

Parameters and explanations are written in each `plugin.md` file.

Testing
-------

To run the test, connect on the munin master/node and execute :

    $ munin-run ping_http_www.google.com
    ping.value 32



Contributing
------------

1. Fork it.
2. Create a branch (`git checkout -b my_markup`)
3. Commit your changes (`git commit -am "Use file_get_contents if CURL is not available"`)
4. Push to the branch (`git push origin my_markup`)
5. Open a [Pull Request][1]
6. Enjoy a refreshing Coke and wait


FAQ
-----------

### Why do I need CURL ?

Because `file_get_contents` fails when connecting in https to websites with an invalid certificate. The only way to do this in PHP is to use CURL with options :

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($ch, CURLOPT_SSLVERSION, 3);



[1]: http://github.com/potsky/MuninPlugins/pulls

