<?php

  // 2500 edges from A to B, every one with a label of its own - as many as the layout takes
  // between two ranks.

  $edges = "{diagram:auto}\n";

  for ( $i = 0; $i < 2500; $i++ )
    $edges .= "A -->|label $i| B\n";

  $edges .= "{/diagram:auto}";

?>
