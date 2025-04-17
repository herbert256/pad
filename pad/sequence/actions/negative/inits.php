<?php

  $pqNegativeOld = $pqResult;
  $pqResult = [];

  foreach ( $pqNegativeOld as $padK => $padV )
    $pqResult [ 'x' . $padK ] = $padV;

  $pqNegativeOld = $pqResult;

?>