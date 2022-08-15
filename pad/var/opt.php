<?php

  pTiming_start ('var');

  include 'inits.php';

  $padOpts = [];

  foreach ( $padData_default_start as $padV )
    $padOpts [] = $padV;

  foreach ( $padExpl as $padV )
    $padOpts [] = trim($padV);

  foreach ( $padData_default_end as $padV )
    $padOpts [] = $padV;

  $padVal = pVar_opts ($padVal, $padOpts);

  return include 'exits.php';

?>