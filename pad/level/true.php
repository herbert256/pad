<?php

  $pPos = $pEnd [$p-1];

a: do {

    $pPos = strpos($pHtml [$p-1] , '{/' . $pTag [$p], $pPos);

    if ($pPos === FALSE)
      return '';

    $pTrue [$p] = substr($pHtml [$p-1], $pEnd [$p-1]+1, $pPos - $pEnd [$p-1] - 1);

    $pPos++;

  } while ( substr_count($pTrue [$p], '{'.$pTag [$p]) <> substr_count($pTrue [$p], '{/'.$pTag[$p]) );
 
   $pPair_check = substr($pHtml[$p], $pPos + strlen($pTag[$p]) + 1, 1);
   if ( ! ($pPair_check == ' ' or $pPair_check == '}' ) )
    goto a;

  $pPair [$p]   = TRUE;
  $pEnd  [$p-1] = strpos ( $pHtml [$p-1], '}', $pPos);

  if ( $pEnd [$p-1] === FALSE )
    return NULL;

  return substr ( $pTrue [$p], 0, $pPos);

?>