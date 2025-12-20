<?php

  $pqActionEnd = $pqResult;
  $pqResult    = [];

  foreach ( $pqActionEnd as $padK => $padV )
    if ( substr ( $padK, 0, 1 ) <> 'x' )
      return include PQ . 'actions/order/values.php';

  return include PQ . 'actions/order/keys.php';

?>
