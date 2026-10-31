<?php

  // A flowchart that is one chain of 8000 steps, A0 --> A1 --> ... - no edge crosses a rank.

  $chain = "{diagram}\n";

  for ( $i = 0; $i < 8000; $i++ )
    $chain .= "A$i --> A" . ( $i + 1 ) . "\n";

  $chain .= "{/diagram}";

?>
