<?php

  // Page arrays named like iteration properties - data, count, name, key, options. A dotted
  // path into one is the array's key: $data.status answered the level's data and ended the
  // page, $count.total the loop's row count, $name.first the loop's name, $key.id the row's key.

  $data    = [ 'title' => 'T', 'status' => 'open' ];
  $count   = [ 'total' => 5 ];
  $name    = [ 'first' => 'Ann' ];
  $key     = [ 'id' => 'k1' ];
  $options = [ 'color' => 'red' ];
  $items   = [ [ 'n' => 1 ], [ 'n' => 2 ] ];

?>
