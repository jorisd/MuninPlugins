
Munin parameters
-----------

### env.url (mandatory)

The url to ping in http or https.  
**eg**: *https://blog.potsky.com*

### env.type (mandatory)

Several values are available :

* **match** : to match a specific word in the web page  
The returned value is the number of milliseconds to download the page or 0 if not available  
A critical alarm is launched if value is not equal to *env.value*

* **size** : to test the web page size in bytes  
The returned value  is the number of bytes of the page 0 if not available  
A critical alarm is launched if value is not equal to *env.value*

* **sizegt** : to test if the web page size is greater than a value in bytes  
The returned value  is the number of bytes of the page 0 if not available  
A critical alarm is launched if value is not greater than *env.value*

* **sizelt** : to test if the web page size is lower than a value in bytes  
The returned value  is the number of bytes of the page 0 if not available  
A critical alarm is launched if value is not lower than *env.value*

### env.value (mandatory)

The value corresponding to the test.

* **eg if type is _match_** : *Potsky*
* **eg if type is _size_** :  *17324*
* **eg if type is _sizegt_** : *100*
* **eg if type is _sizelt_** : *20000*

### env.title

The title of the graph.  
**default**: the right part next to character _ of the plugin name.  
**eg**: *Potsky Blog*

### env.timeout

The timeout of the connexion in seconds.  
A warning is launch when the the connection duration is greater than `timeout*2/3`.  
**default**: 5  
**eg**: *10*

### env.warning
Enable or disable the warning alert.  
If set to *no*, no warning alert will be triggered.  
**default**: yes  
**eg**: *no*