<?php

  include PQ . 'build/randomly/init.php';

  $pqGo = $pqFrom;

  while ( $pqGo <= $pqTo ) {

    $pqLoop = $pqGo;

    if ( ! include PQ . 'build/one.php')
      break;

    if ( $pqRandomInc )
      $pqInc = pqRandomParm3 ( $pqRandomInc );

    $pqGo = $pqGo + $pqInc;

  }

?>
