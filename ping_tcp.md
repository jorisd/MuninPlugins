Munin parameters
-----------

### env.url (mandatory)

The url to ping with a TCP socket.  
**eg**: *blog.potsky.com*

### env.port (mandatory)

The port of the TCP socket.  
**eg**: *22*

### env.title

The title of the graph.  
**default**: the right part next to character _ of the plugin name.  
**eg**: *Potsky Blog SSH*

### env.timeout

The timeout of the connexion in seconds.  
A warning is launch when the the connection duration is greater than `timeout*2/3`.  
**default**: 5  
**eg**: *10*
