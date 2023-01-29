<?php

  $padValBase = $padVal;

  $padVal = padVarOpts ($padVal, $padOpts);

  if ( $padTrace ) 
    include 'trace.php';

  padTimingEnd ('var');

  return $padVal;

?>