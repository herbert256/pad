<?php

  // Fixture for pages/callback: a streaming callback, so the row phase runs during the iteration
  // and reads the occurrence's own fields as plain variables. _callbacks/before.php and
  // demand.php are the sandbox pair, which cannot do this.

  switch ( $padCallback ) {

    case 'init' : $sum = 0;      break;
    case 'row'  : $sum += $n;    break;
    case 'exit' : $sum = "[$sum]";  break;

  }

?>