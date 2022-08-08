<?php

  $pPos = $pEnd [$p-1];

  do {

    $pPos = strpos($pHtml [$p-1] , '{/' . $pTag [$p], $pPos);

    if ($pPos === FALSE)
      return '';

    $pTrue [$p] = substr($pHtml [$p-1], $pEnd [$p-1]+1, $pPos - $pEnd [$p-1] - 1);

    $pPos++;

  } while ( substr_count($pTrue [$p], '{'.$pTag [$p]) <> substr_count($pTrue [$p], '{/'.$pTag[$p]) );
 
  $pPair [$p]   = TRUE;
  $pTrue [$p]   = substr ( $pTrue [$p], 0, $pPos);
  $pEnd  [$p-1] = strpos ( $pHtml [$p-1], '}', $pPos+2);

  if ( $pEnd [$p-1] === FALSE )
    return NULL;

  return $pTrue [$p];

?>