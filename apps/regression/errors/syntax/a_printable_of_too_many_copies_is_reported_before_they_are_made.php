<?php

  // 10000 rows, copies=1000: ten million labels, reported as more than 5000 - counted, not
  // made. Every copy was made before the count, and ten million of them took 256 MB, past
  // the memory a stock PHP has: a fatal error where the strict check names the printable.

  ini_set ( 'memory_limit', '96M' );

  $rows = array_fill ( 0, 10000, [ 'n' => 1 ] );

?>
