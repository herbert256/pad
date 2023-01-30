<?php

  $padPos = $padEnd [$padP];

  while (1) {

    do {

      $padPos = strpos($padHtml [$padP] , '{/' . $padTag [$pad], $padPos);

      if ($padPos === FALSE) {
        $padTrue [$pad] = '';
        return FALSE;
      } 

      $padTrue [$pad] = substr($padHtml [$padP], $padEnd [$padP]+1, $padPos - $padEnd [$padP] - 1);

      $padPos++;

    } while ( substr_count($padTrue [$pad], '{'.$padTag [$pad]) <> substr_count($padTrue [$pad], '{/'.$padTag[$pad]) );

    $padPairCheck = substr($padHtml [$padP], $padPos + strlen($padTag[$pad]) + 1, 1);
    
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