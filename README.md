Mapstorming
==============

Grunt task to export and/or upload mbtiles to mapbox.

###Create Tilemill project

Execute CLI and follow instructions:

~~~
$ php mapstorming.php process
~~~

###Add a new City

~~~
$ php mapstorming.php add-city
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