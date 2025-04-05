<?php

  $padSeqBuildType = 'fixed';

  include 'sequence/build/randomly/init.php';
  
  $padSeqTry = PHP_INT_MAX;

  $padSeqFixed = array_values ( $padSeqFixed );

  foreach ( $padSeqFixed as $padSeqKey => $padSeqLoop ) {

    if ( $padSeqKey < $padSeqStart - 1 ) continue;
    if ( $padSeqKey > $padSeqEnd - 1  ) return;

    $padSeqTries = $padSeqKey;

    if ( ! include 'sequence/build/one.php')
      break;

  }

  return;


  $padSeqTry = PHP_INT_MAX;

  $padSeqFixed = array_values ( $padSeqFixed );

  $padSeqLoop = reset ( $padSeqFixed );

  while ( $padSeqLoop !== FALSE) {

    $padSeqKey = key ( $padSeqFixed );

    if ( $padSeqKey < $padSeqStart - 1 ) continue;
    if ( $padSeqKey > $padSeqEnd - 1  ) return;

    $padSeqTries = $padSeqKey;

    if ( ! include 'sequence/build/one.php')
      break;

    if ( $padSeqInc > 1 )
      for ( $padI=1; $padI < $padSeqInc; $padI++ ) 
        if ( next ( $padSeqFixed ) === FALSE )
          break 2;

    $padSeqLoop = next ( $padSeqFixed );

  }

?>