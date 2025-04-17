<?php

  include 'sequence/build/randomly/init.php';

  $pqSkipNow   = 0;
  $pqSkipCount = 0;

  $pqTry = PHP_INT_MAX;

  if ( is_array($pqFixed))
    $pqFixed = array_values ( $pqFixed );
  else
    $pqFixed = [];

  foreach ( $pqFixed as $pqKey => $pqLoop ) {

    if ( $pqSkipNow ) {
      $pqSkipCount++;
      if ( $pqSkipCount < $pqInc )
        continue;
      $pqSkipCount = $pqSkipNow = 0;
    }

    if ( $pqKey < $pqStart - 1 ) continue;
    if ( $pqKey > $pqEnd - 1   ) return;

    $pqTries = $pqKey;

    if ( ! include 'sequence/build/one.php')
      break;

    if ( $pqInc > 1 and ! in_array ( 'increment', $pqDone ) )
      $pqSkipNow = 1;

  }

?>