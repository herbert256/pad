<?php

  // Two RECORD reads in one PHP file, the first iterated twice, and a plain list after them:
  // each record is one occurrence wherever and however often it is used, and the list one
  // occurrence per value.

  $jim    = db ( "record name, phone from staff where name = 'jim'" );
  $joe    = db ( "record name, phone from staff where name = 'joe'" );
  $colors = [ 'red', 'green', 'blue' ];

?>
