<?php

  $padPairSet    = FALSE;
  $padTrueSet    = '';
  $padPrmTypeSet = ( count($padWords) > 1 ) ? 'open' : 'none';

  if ( substr($padBetween, -1) == '/') {
    $padBetween = substr($padBetween, 0, -1);
    $padWords   = preg_split ("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);
    return TRUE;
  }

  $padPairTag    = ( $padTypeGiven ) ? $padTypeResult . ':' . $padTypeCheck : $padTypeCheck;
  $padPos        = $padEnd [$pad];
  $padPairCheck  = '';

  while ( ! in_array ( $padPairCheck, [' ', '}'] ) ) {

    $padPos = strpos($padHtml [$pad] , '{/' . $padPairTag, ++$padPos);

    if ($padPos === FALSE)
      return TRUE;

    $padTrueBase = substr($padHtml [$pad], $padEnd [$pad]+1, $padPos - $padEnd [$pad] - 1);

    if ( padOpenCloseCountOne ( $padTrueBase, $padPairTag) )
      $padPairCheck = substr($padHtml [$pad], $padPos + strlen($padPairTag) + 2, 1);
    
  }
 
  $padTrueSet    = substr ( $padTrueBase, 0, $padPos );
  $padEnd [$pad] = strpos ( $padHtml [$pad], '}', $padPos+2 );

  if ( $padEnd [$pad] === FALSE )
    return FALSE;

  $padPairSet      = TRUE;
  $padBetweenCheck = substr ($padHtml [$pad], $padPos+1, $padEnd [$pad]-$padPos-1);
  $padWordsCheck   = preg_split ("/[\s]+/", $padBetweenCheck, 2, PREG_SPLIT_NO_EMPTY);

  if ( count($padWordsCheck) > 1 and $padPrmTypeSet == 'open' )
    return padError ("Both open and close parameters given");

  if ( count($padWordsCheck) > 1 ) { 
    $padBetween    = $padBetweenCheck;  
    $padBusy       = "$padPage --> {/" . $padBetween . '}';
    $padWords      = $padWordsCheck;
    $padPrmTypeSet = 'close';
  }
 
  return TRUE;

?>