<?php

  $padBaseSet = substr ( $padBaseBase, 0, $padPos );
  $padPairSet = TRUE;

  $padEnd [$pad] = $padPos;

  do {

    $padEnd [$pad] = strpos ( $padOut [$pad], '}', $padEnd [$pad] + 1 );

    if ( $padEnd [$pad] === FALSE )
      padError ("Closing } not found");

    $padBetweenCheck = substr ($padOut [$pad], $padPos+1, $padEnd [$pad]-$padPos-1);

  } while ( substr_count($padBetweenCheck, '{') <> substr_count($padBetweenCheck, '}') );

  $padWordsCheck = preg_split ( "/[\s]+/", $padBetweenCheck, 2, PREG_SPLIT_NO_EMPTY );

  if ( count($padWordsCheck) > 1 ) { 
    $padPrmTypeSet = 'close';
    $padBetween    = $padBetweenCheck;  
    include 'level/between.php';
  }

?>