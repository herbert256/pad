<?php

  // A heatmap's time goes by the cells it has, not by every pair of a row and a column:
  // 20,000 rows of a cell each - each its own row and column - took ten seconds.

  $cells = [];

  for ( $i = 0; $i < 20000; $i++ )
    $cells [] = [ 'r' => "r$i", 'c' => "c$i", 'v' => $i % 7 ];

  $start = hrtime ( TRUE );

?>
