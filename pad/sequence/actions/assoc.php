<?php

  // Prefixes every key of $pqResult with 'x' before the actions run, keeping the
  // unprefixed copy in $pqActionStart. Actions that preserve keys leave the 'x' in
  // place, which is how order/ and negative/ later tell a key-preserving action from one
  // that rebuilt the array and can only be matched by value.

  $pqActionStart = $pqResult;
  $pqResult      = [];

  foreach ( $pqActionStart as $padK => $padV )
    $pqResult [ 'x' . $padK ] = $padV;

?>