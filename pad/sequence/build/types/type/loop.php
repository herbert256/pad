<?php

  include 'sequence/build/randomly/init.php';

  $pqGo = $pqFrom;

  while ( $pqGo <= $pqTo ) {

    $pqLoop = $pqGo;

    $pqTmp = include 'sequence/build/one.php';
    if ( $pqTmp === FALSE )
      break;

    $pqGo = $pqGo + $pqInc;

  }

?>