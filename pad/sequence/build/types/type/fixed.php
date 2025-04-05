<?php

  $padSeqBuildType = 'fixed';

  include 'sequence/build/randomly/init.php';

  $padSeqSkipNow   = 0;
  $padSeqSkipCount = 0;

  $padSeqTry = PHP_INT_MAX;

  $padSeqFixed = array_values ( $padSeqFixed );

  foreach ( $padSeqFixed as $padSeqKey => $padSeqLoop ) {

    if ( $padSeqSkipNow ) {
      $padSeqSkipCount++;
      if ( $padSeqSkipCount < $padSeqInc )
        continue;
      $padSeqSkipCount = $padSeqSkipNow = 0;
    }

    if ( $padSeqKey < $padSeqStart - 1 ) continue;
    if ( $padSeqKey > $padSeqEnd - 1   ) return;

    $padSeqTries = $padSeqKey;

    if ( ! include 'sequence/build/one.php')
      break;

    if ( $padSeqInc > 1 )
      $padSeqSkipNow = 1;

  }

?>