<?php

  padTimingStart ('var');

  include 'inits.php';

  $padOpts = [];

  foreach ( $padDataDefaultStart as $padV )
    $padOpts [] = $padV;

  foreach ( $padExpl as $padV )
    $padOpts [] = trim($padV);

  foreach ( $padDataDefaultEnd as $padV )
    $padOpts [] = $padV;

  $padVal = padVarOpts ($padVal, $padOpts);

  return include 'exits.php';

?>