<?php

  // Return shape for a tag that was given an explicit name=: every term becomes a row with
  // one field, under that name, so only {$thatName} reaches the value. Reads $pqResult and
  // $pqNameGiven, fills $padData[$pad].

  foreach ( $pqResult as $pqValue )
    $padData [$pad] [] [$pqNameGiven] = $pqValue;

?>