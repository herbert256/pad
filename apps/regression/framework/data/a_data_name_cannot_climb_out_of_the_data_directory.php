<?php

  // A data name is a file in a _data directory. One that climbs out of it with ../ - here
  // to a .php file beside this case - is no data name at all: a name a visitor sent would
  // otherwise include and run any .php file on the disk.

  $name = '../data/_outside';

?>
