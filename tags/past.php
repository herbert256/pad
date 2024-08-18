<?php

  for ( $padPastIdx=0; $padPastIdx < $pad; $padPastIdx++) { 

    $padPastSubject = substr ( $padPad [$padPastIdx], 0, $padStart [$padPastIdx] );

    if ( substr_count ( $padPastSubject, "&future;$padParm&future;" ) ) {

      $padPastLenght  = strlen ( $padPastSubject );
      $padPastSubject = str_replace ( "&future;$padParm&future;", $padContent, $padPastSubject );
      $padPastDiff    = strlen ( $padPastSubject ) - $padPastLenght; 

      $padPad   [$padPastIdx] = $padPastSubject . substr ( $padPad [$padPastIdx], $padStart [$padPastIdx] );
      $padStart [$padPastIdx] = $padStart [$padPastIdx] + $padPastDiff;
      $padEnd   [$padPastIdx] = $padEnd   [$padPastIdx] + $padPastDiff;

    }

  }

  return NULL;

?>