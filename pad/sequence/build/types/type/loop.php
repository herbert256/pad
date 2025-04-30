<?php

  include 'sequence/build/randomly/init.php';

  $pqGo = $pqFrom;

  while ( $pqGo <= $pqTo ) {

    $pqLoop = $pqGo;

    if ( ! include 'sequence/build/one.php')
      break;

    $pqGo = $pqGo + $pqInc;

  }

?>