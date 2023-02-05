<?php

  $padPairTag        = ( $padGiven [$pad]   ) ? $padType [$pad] . ':' . $padTag [$pad] : $padTag [$pad];
  $padPrmType [$pad] = ( $padPrm [$pad] [0] ) ? 'open' : 'none';
  $padPos            = $padEnd [$pad-1];
  $padPairCheck      = '';

  while ( ! in_array ( $padPairCheck, [' ', '}'] ) ) {

    $padPos = strpos($padHtml [$pad-1] , '{/' . $padPairTag, $padPos);

    if ($padPos === FALSE)
      return TRUE;

    $padTrueBase = substr($padHtml [$pad-1], $padEnd [$pad-1]+1, $padPos - $padEnd [$pad-1] - 1);

    if ( padOpenCloseCountOne ( $padTrueBase, $padPairTag) )
      $padPairCheck = substr($padHtml [$pad-1], $padPos + strlen($padPairTag) + 2, 1);
    
    $padPos++;

  }
 
  $padTrue [$pad]  = substr ( $padTrueBase, 0, $padPos );
  $padEnd [$pad-1] = strpos ( $padHtml [$pad-1], '}', $padPos+2 );

  if ( $padEnd [$pad-1] === FALSE )
    return padIgnore ('pair', 1);

  $padBetween = substr ($padHtml [$pad-1], $padPos+1, $padEnd [$pad-1]-$padPos-1);
  $padWords   = preg_split ("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);

  if ( count ($padWords) > 1 ) {
    include 'parms.php'; 
    $padPrmType [$pad] = 'close';
  }

  $padPair [$pad] = TRUE;

  return TRUE;

?>