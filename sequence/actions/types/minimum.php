<?php

  asort ( $pqResult );

  if ( $pqAction == 'minimum' )   
    $pqResult = array_slice ( $pqResult, 0,                 $pqActionCnt, true );
  else
    $pqResult = array_slice ( $pqResult, $pqActionCnt * -1, $pqActionCnt, true );

  $pqResult = include 'sequence/actions/order/order.php';

?>