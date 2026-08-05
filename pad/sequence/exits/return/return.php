<?php

  // Return stage of the wind-down: shapes what the tag hands to the template. Clears
  // $padData[$pad] and refills it from $pqResult, one row per term - under the single name
  // the tag was given (name=), otherwise under every name the sequence answers to.

  $padData [$pad] = [];

  if ( $pqNameGiven ) include PQ . 'exits/return/given.php';
  else                include PQ . 'exits/return/names.php';

?>