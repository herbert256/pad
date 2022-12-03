<?php

  include 'inits.php';

  $padVal = padVarOpts ($padVal, $padOpts);
  $padVal = str_replace ( '}', '&close;', $padVal );

  return include 'exits.php';

?>