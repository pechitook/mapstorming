Mapstorming
==============

Grunt task to export and/or upload mbtiles to mapbox.


###Add a new City

~~~
$ php mapstorming.php add-city
~~~

###Create Tilemill project

Execute CLI and follow instructions:

~~~
$ php mapstorming.php process
~~~


###Export MBTiles to tilemill_project/mbtiles

~~~
$ grunt exportMbtiles
~~~

###Upload directly into Mapbox

First, open Tilemill and log in with your account, then you can run:

~~~
$ grunt uploadMbtiles
~~~