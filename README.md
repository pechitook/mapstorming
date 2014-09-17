grunt-tilemill
==============

Grunt task to export and/or upload mbtiles to mapbox.

#Create Tilemill project

Execute CLI and follow instructions:

~~~
$ php project_template.php
~~~

#Export MBTiles to tilemill_project/mbtiles

~~~
$ grunt exportMbtiles
~~~

#Upload directly into Mapbox

First, open Tilemill and log in with your account, then you can run:

~~~
$ grunt uploadMbtiles
~~~