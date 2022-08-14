<?php

  $pPos = $pEnd [$pP];

  while (1) {

    do {

      $pPos = strpos($pHtml [$pP] , '{/' . $pTag [$p], $pPos);

      if ($pPos === FALSE) {
        $pTrue [$p] = '';
        return FALSE;
      } 

      $pTrue [$p] = substr($pHtml [$pP], $pEnd [$pP]+1, $pPos - $pEnd [$pP] - 1);

      $pPos++;

    } while ( substr_count($pTrue [$p], '{'.$pTag [$p]) <> substr_count($pTrue [$p], '{/'.$pTag[$p]) );

    $pPair_check = substr($pHtml [$pP], $pPos + strlen($pTag[$p]) + 1, 1);
    
    if ( ! ($pPair_check == ' ' or $pPair_check == '}' ) )
      continue;

    break;

  }
 
  $pTrue [$p] = substr ( $pTrue [$p], 0, $pPos );
  $pEnd [$pP] = strpos ( $pHtml [$pP], '}', $pPos+2 );

  if ( $pEnd [$pP] === FALSE )
    return NULL;

  $pBetween = substr ($pHtml [$pP], $pPos+1, $pEnd [$pP]-$pPos-1);
  $pWords   = preg_split ("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);

  if ( count ($pWords) > 1 ) {
    include 'between.php';
    $pPrmsType [$p] = 'close';
  }

  return TRUE;

?>