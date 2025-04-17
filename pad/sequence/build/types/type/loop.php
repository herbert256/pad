<?php

  include 'sequence/build/randomly/init.php';

  $pqGo = $pqStart;

  while ( $pqGo <= $pqEnd ) {

    $pqLoop = $pqGo;

    $pqTmp = include 'sequence/build/one.php';
    if ( $pqTmp === FALSE )
      break;

    $pqGo = $pqGo + $pqInc;

  }

?>