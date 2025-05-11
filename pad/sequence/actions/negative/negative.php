<?php

  if ( ! $pqNegative )
    return;

  $pqActionEnd = $pqResult;
  $pqResult    = [];

  foreach ( $pqActionEnd as $padK => $padV ) 
    if ( substr ( $padK, 0, 1 ) <> 'x' ) 
      return include 'sequence/actions/negative/values.php';

  return include 'sequence/actions/negative/keys.php';

?>