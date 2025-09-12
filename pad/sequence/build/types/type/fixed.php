<?php

  include 'sequence/build/randomly/init.php';

  $pqSkipNow   = 0;
  $pqSkipCount = 0;
  $pqTry       = PHP_INT_MAX;
  $pqFixed     = array_values ( $pqFixed );

  foreach ( $pqFixed as $pqKey => $pqLoop ) {

    if ( $pqSkipNow ) {
      $pqSkipCount++;
      if ( $pqSkipCount < $pqInc )
        continue;
      $pqSkipCount = $pqSkipNow = 0;
    }

    if ( $pqKey < $pqFrom - 1 ) continue;
    if ( $pqKey > $pqTo - 1   ) break;

    $pqTries = $pqKey;

    if ( ! include 'sequence/build/one.php')
      break;

    if ( $pqRandomInc )
      $pqInc = pqRandomParm3 ( $pqRandomInc );

    if ( $pqInc > 1 and ! in_array ( 'increment', $pqDone ) )
      $pqSkipNow = 1;

  }

?>