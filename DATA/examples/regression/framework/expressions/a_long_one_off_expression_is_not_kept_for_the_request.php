<?php

  // Four hundred rows, each splicing a body of 10 KB of its own into an expression: kept for
  // the request, the texts held some 20 MB after the loop; the 4 MB of the values themselves
  // stay, kept by padSpliceRecord for the guards of the splice.

  $rows = [];

  for ( $i = 0; $i < 400; $i++ )
    $rows [] = [ 'body' => str_repeat ( 'lorem ipsum ', 850 ) . $i ];

  $before = memory_get_usage ();

?>
