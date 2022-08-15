<?php

  pTiming_start ('var');

  include 'inits.php';

  $padOpts = $padExpl;
  $padVal  = pVar_opts ($padVal, $padOpts);
  $padVal  = str_replace ( '}', '&close;', $padVal );

  return include 'exits.php';

?>