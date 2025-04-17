<?php

  include 'sequence/build/randomly/init.php';

  $pqLoop = $pqStart;

  while ( $pqLoop <= $pqEnd )
    if ( ! include 'sequence/build/one.php')
      break;

?>