<?php

  $pPos = $pEnd [$p];

  while (1) {

    do {

      $pPos = strpos($pHtml [$p] , '{/' . $pTag [$pN], $pPos);

      if ($pPos === FALSE) {
        $pTrue [$pN] = '';
        return FALSE;
      } 

      $pTrue [$pN] = substr($pHtml [$p], $pEnd [$p]+1, $pPos - $pEnd [$p] - 1);

      $pPos++;

    } while ( substr_count($pTrue [$pN], '{'.$pTag [$pN]) <> substr_count($pTrue [$pN], '{/'.$pTag[$pN]) );

    $pPair_check = substr($pHtml [$p], $pPos + strlen($pTag[$pN]) + 1, 1);
    if ( ! ($pPair_check == ' ' or $pPair_check == '}' ) )
      continue;

    break;

  }
 
  $pTrue [$pN]  = substr ( $pTrue [$pN], 0, $pPos );
  $pEnd [$p] = strpos ( $pHtml [$p], '}', $pPos+2 );

  if ( $pEnd [$p] === FALSE )
    return NULL;

  $pBetween = substr ($pHtml [$p], $pPos+1, $pEnd [$p]-$pPos-1);
  $pWords   = preg_split ("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);

  if ( count ($pWords) > 1 ) {
    include 'between.php';
    $pPrmsType [$pN] = 'close';
  }

  return TRUE;

?>