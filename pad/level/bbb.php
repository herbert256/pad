<?php

  $pPos = $pEnd [$p-1];

go2:  


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
  if ( ! ($pPair_check == ' ' or $pPair_check == '}' or $pPair_check ==  ',') )
    goto go2;
 
  $pTrue [$p] = substr ($pTrue [$p], 0, $pPos);

  $pEnd [$p-1] = strpos ( $pHtml [$p-1], '}', $pPos+2);

  if ( $pEnd [$p-1] === FALSE )
    return NULL;

  $pTmp = substr ($pHtml [$p-1], $pPos+1, $pEnd [$p-1]-$pPos-1);

  while ( substr_count($pTmp, '{') <> substr_count($pTmp, '}') ) {

    if ( $pEnd [$p-1] === FALSE or $pEnd [$p-1] + 1 == strlen($pHtml [$p-1]) )
       break;

    $pEnd [$p-1] = strpos ( $pHtml [$p-1], '}', $pEnd [$p-1] + 1); 
    if ( $pEnd [$p-1] !== FALSE )
      $pTmp = substr ($pHtml [$p-1], $pPos+1, $pEnd [$p-1]-$pPos-1);

  }

  if ( $pEnd [$p-1] === FALSE )
    $pEnd [$p-1] = strpos ( $pHtml [$p-1], '}', $pPos+2);

  $pBetween = substr ($pHtml [$p-1], $pPos+1, $pEnd [$p-1]-$pPos-1);
  $pWords   = preg_split ("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);

  if ( count ($pWords) > 1 ) {
    include 'between.php';
    $pPrmsType [$p] = 'close';
  }

  $pOpen_close = [];
  $pOpen_close [$pTag [$p]] = TRUE;

  $pPos = strpos($pTrue [$p], '{/', 0);

  while ($pPos !== FALSE) {
    $pPos2 = strpos($pTrue [$p], '}', $pPos);
    if ( $pPos2 !== FALSE ) {
      $pPos3 = strpos($pTrue [$p], ' ', $pPos);
      if ($pPos3 !== FALSE and $pPos3 < $pPos2 )
        $pPos2 = $pPos3;      
      $pCheckTag = substr($pTrue [$p], $pPos+2, $pPos2-$pPos-2);
      if ( pValid ($pCheckTag) )
        $pOpen_close [$pCheckTag] = TRUE;
    }
    $pPos = strpos($pTrue [$p], '{/', $pPos+1);
  }

  $pPos = -1;

go: $pPos++;
    $pPos = strpos($pTrue [$p], '{else}', $pPos);

  if ( $pPos === FALSE )
    return TRUE;
  
  $pCheck = substr($pTrue [$p],0,$pPos);

  foreach ( $pOpen_close as $pCheckTag => $pDummy_var )
    if ( ( substr_count($pCheck, '{'.$pCheckTag.' ' ) + substr_count($pCheck, '{'.$pCheckTag.'}' ) )
           <> 
         ( substr_count($pCheck, '{/'.$pCheckTag.' ') + substr_count($pCheck, '{/'.$pCheckTag.'}') ) )
      goto go;

  $pFalse [$p] = substr ( $pTrue [$p], $pPos+6  );
  $pTrue  [$p] = substr ( $pTrue [$p], 0, $pPos );

  return TRUE;

?>