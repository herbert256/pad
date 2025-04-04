<?php

  $padSeqBuildType = 'fixed';
  
  $padSeqTry = PHP_INT_MAX;

  $padSeqFixed = array_values ( $padSeqFixed );

  foreach ( $padSeqFixed as $padSeqKey => $padSeqLoop ) {

    if ( $padSeqKey < $padSeqStart - 1 ) continue;
    if ( $padSeqKey > $padSeqEnd - 1  ) return;

    $padSeqTries = $padSeqKey;

    if ( ! include 'sequence/build/one.php')
      break;

  }

?>