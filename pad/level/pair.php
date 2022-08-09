<?php

  $pPos = $pEnd [$p-1];

  while (1) {

    do {

      $pPos = strpos($pHtml [$p-1] , '{/' . $pTag [$p], $pPos);

      if ($pPos === FALSE) {
        $pTrue [$p] = '';
        return FALSE;
      } 

      $pTrue [$p] = substr($pHtml [$p-1], $pEnd [$p-1]+1, $pPos - $pEnd [$p-1] - 1);

      $pPos++;

    } while ( substr_count($pTrue [$p], '{'.$pTag [$p]) <> substr_count($pTrue [$p], '{/'.$pTag[$p]) );

    $pPair_check = substr($pHtml [$p-1], $pPos + strlen($pTag[$p]) + 1, 1);
    if ( ! ($pPair_check == ' ' or $pPair_check == '}' ) )
      continue;

    break;

  }
 
  $pTrue [$p]  = substr ( $pTrue [$p], 0, $pPos );
  $pEnd [$p-1] = strpos ( $pHtml [$p-1], '}', $pPos+2 );

  if ( $pEnd [$p-1] === FALSE )
    return NULL;

  $pBetween = substr ($pHtml [$p-1], $pPos+1, $pEnd [$p-1]-$pPos-1);
  $pWords   = preg_split ("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);

  if ( count ($pWords) > 1 ) {
    include 'between.php';
    $pPrmsType [$p] = 'close';
  }

  return TRUE;

?>