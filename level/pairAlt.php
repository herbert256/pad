<?php

  $padPairSet    = FALSE;
  $padTrueSet    = '';
  $padPrmTypeSet = ( count($padWords) > 1 ) ? 'open' : 'none';

  if ( substr($padBetween, -1) == '/') {
    $padBetween = substr($padBetween, 0, -1);
    $padWords   = preg_split ("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);
    return TRUE;
  }

  $padPairTag      = ( $padTypeGiven ) ? $padTypeResult . ':' . $padTypeCheck : $padTypeCheck;
  $padPairTagClose = $padPairTag;
 
  $padPairCheck = include 'pairCheck.php';
  if ( $padPairCheck == NULL)
    return FALSE;

  if ( $padPairCheck == FALSE) {
    $padPairTagClose = '';
    $padPairCheck = include 'pairCheck.php';
    if ( $padPairCheck == NULL)
      return FALSE;
  }
  
  if ( $padPairCheck == TRUE) {

    $padPairSet      = TRUE;
    $padBetweenCheck = substr ($padHtml [$pad], $padPos+1, $padEnd [$pad]-$padPos-1);
    $padWordsCheck   = preg_split ("/[\s]+/", $padBetweenCheck, 2, PREG_SPLIT_NO_EMPTY);

    if ( count($padWordsCheck) > 1 and $padPrmTypeSet == 'open' )
      return padError ("Both open and close parameters given");

    if ( count($padWordsCheck) > 1 ) { 
      $padBetween    = $padBetweenCheck;  
      $padWords      = $padWordsCheck;
      $padPrmTypeSet = 'close';
    }

  }
 
  return TRUE;

?>