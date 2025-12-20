<?php

  if ( ! $pqNegative )
    return;

  $pqActionEnd = $pqResult;
  $pqResult    = [];

  foreach ( $pqActionEnd as $padK => $padV )
    if ( substr ( $padK, 0, 1 ) <> 'x' )
      return include PQ . 'actions/negative/values.php';

  return include PQ . 'actions/negative/keys.php';

?>
