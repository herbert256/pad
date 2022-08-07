<?php

  $pTrue[$pT] = $pFalse[$pT] = '';
  $pPair[$pT]   = TRUE;

  $pPos = $pEnd[$p];

go2:  
  do {

    $pPos = strpos($pHtml[$p] , '{/' . $pTag[$p], $pPos);

    if ($pPos === FALSE) {

      $pTrue[$pT] = '';
      $pPair[$pT] = FALSE;

      return FALSE;
 
    } 

    $pTrue[$pT] = substr($pHtml[$p], $pEnd[$p]+1, $pPos - $pEnd[$p] - 1);

    $pPos++;

  } while ( substrCnt($pTrue[$pT], '{'.$pTag[$p]) <> substrCnt($pTrue[$pT], '{/'.$pTag) );

  $pPair_check = substr($pHtml[$p], $pPos + strlen($pTag) + 1, 1);
  if ( ! ($pPair_check == ' ' or $pPair_check == '}' or $pPair_check ==  ',') )
    goto go2;
 
  $pTrue[$pT] = substr ($pTrue[$pT], 0, $pPos);

  $pEnd[$p] = strpos ( $pHtml[$p], '}', $pPos+2);
  if ( $pEnd[$p] === FALSE )
    return FALSE;

  $pTmp = substr ($pHtml[$p], $pPos+1, $pEnd[$p]-$pPos-1);

  while ( substrCnt($pTmp, '{') <> substrCnt($pTmp, '}') ) {

    if ( $pEnd[$p] === FALSE or $pEnd[$p] + 1 == strlen($pHtml[$p]) )
       break;

    $pEnd[$p] = strpos ( $pHtml[$p], '}', $pEnd[$p] + 1); 
    if ( $pEnd[$p] !== FALSE )
      $pTmp = substr ($pHtml[$p], $pPos+1, $pEnd[$p]-$pPos-1);

  }

  if ( $pEnd[$p] === FALSE )
    $pEnd[$p] = strpos ( $pHtml[$p], '}', $pPos+2);

  $pBetween = substr ($pHtml[$p], $pPos+1, $pEnd[$p]-$pPos-1);
  include 'between.php';

  if ($pPrms)
    $pPrmsType[$p] = 'close';

  $pOpen_close = [];
  $pOpen_close [$pTag[$p]] = TRUE;

  $pPos = strpos($pTrue[$pT], '{/', 0);

  while ($pPos !== FALSE) {
    $pPos2 = strpos($pTrue[$pT], '}', $pPos);
    if ( $pPos2 !== FALSE ) {
      $pPos3 = strpos($pTrue[$pT], ' ', $pPos);
      if ($pPos3 !== FALSE and $pPos3 < $pPos2 )
        $pPos2 = $pPos3;      
      $pCheckTag = substr($pTrue[$pT], $pPos+2, $pPos2-$pPos-2);
      if ( pValid ($pCheckTag) )
        $pOpen_close [$pCheckTag] = TRUE;
    }
    $pPos = strpos($pTrue[$pT], '{/', $pPos+1);
  }

  $pPos = -1;

go: $pPos++;
    $pPos = strpos($pTrue[$pT], '{else}', $pPos);

  if ( $pPos === FALSE )
    return TRUE;
  
  $pCheck = substr($pTrue[$pT],0,$pPos);

  foreach ( $pOpen_close as $pCheckTag => $pDummy_var )
    if ( ( substrCnt($pCheck, '{'.$pCheckTag.' ' ) + substrCnt($pCheck, '{'.$pCheckTag.'}' ) )
           <> 
         ( substrCnt($pCheck, '{/'.$pCheckTag.' ') + substrCnt($pCheck, '{/'.$pCheckTag.'}') ) )
      goto go;

  $pFalse[$pT] = substr ( $pTrue[$pT], $pPos+6  );
  $pTrue[$pT]  = substr ( $pTrue[$pT], 0, $pPos );

  return TRUE;

?>