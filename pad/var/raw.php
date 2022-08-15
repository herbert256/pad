<?php

  padTimingStart ('var');

  include 'inits.php';

  $padOpts = $padExpl;
  $padVal  = padVarOpts ($padVal, $padOpts);
  $padVal  = str_replace ( '}', '&close;', $padVal );

  return include 'exits.php';

?>