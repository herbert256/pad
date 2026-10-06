<?php

  // The page's PHP runs out of memory: a fatal no handler catches, reported from the
  // shutdown hook - which had no memory left either, and the request ended as a bare 500
  // with nothing in it and nothing in the log.

  ini_set ( 'memory_limit', memory_get_usage () + 8 * 1048576 );

  $hoard = [];

  while ( TRUE )
    $hoard [] = str_repeat ( 'y', 100000 );

?>
