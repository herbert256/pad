<?php

  if ( $pPos === FALSE)
    return TRUE;

  $pBetween = substr ($pHtml [$p-1], $pPos+1, $pEnd [$p-1]-$pPos-1);
  $pWords   = preg_split ("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);

  if ( count ($pWords) > 1 ) {
    include 'between.php';
    $pPrmsType [$p] = 'close';
  }

  return TRUE;

?>