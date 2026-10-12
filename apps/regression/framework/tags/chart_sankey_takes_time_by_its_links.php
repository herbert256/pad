<?php

  // The flows into a sankey node are looked up by its name, not searched for in every link
  // per node: 10,000 nodes and 20,000 links took six seconds.

  $flows = [];

  for ( $i = 0; $i < 10000; $i++ ) {
    $flows [] = [ 's' => "a$i", 't' => "b$i", 'v' => 1 ];
    $flows [] = [ 's' => "b$i", 't' => 'c' . ( $i % 100 ), 'v' => 1 ];
  }

  $start = hrtime ( TRUE );

?>
