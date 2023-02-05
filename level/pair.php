<?php

  if ( $padGiven [$pad] )
    $padPairTag = $padType [$pad] . ':' . $padTag [$pad];
  else
    $padPairTag = $padTag [$pad];

  $padPrmType [$pad] = ( $padPrm [$pad] [0] ) ? 'open' : 'none';

  $padPos = $padEnd [$padP];

  while (1) {

    do {


      $padPos = strpos($padHtml [$padP] , '{/' . $padPairTag, $padPos);

      if ($padPos === FALSE) {
        $padTrue [$pad] = '';
        return FALSE;
      } 

      $padTrue [$pad] = substr($padHtml [$padP], $padEnd [$padP]+1, $padPos - $padEnd [$padP] - 1);

      $padPos++;


    } while ( ! padOpenCloseCountOne ( $padTrue [$pad], $padPairTag) );

    $padPairCheck = substr($padHtml [$padP], $padPos + strlen($padPairTag) + 1, 1);
    
    if ( ! ($padPairCheck == ' ' or $padPairCheck == '}' ) )
      continue;

    break;

  }
 
  $padTrue [$pad] = substr ( $padTrue [$pad], 0, $padPos );
  $padEnd [$padP] = strpos ( $padHtml [$padP], '}', $padPos+2 );

  if ( $padEnd [$padP] === FALSE )
    return NULL;

  $padBetween = substr ($padHtml [$padP], $padPos+1, $padEnd [$padP]-$padPos-1);
  $padWords   = preg_split ("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);

  if ( count ($padWords) > 1 ) {
    include 'parms.php'; 
    $padPrmType [$pad] = 'close';
  }

  return TRUE;

?>