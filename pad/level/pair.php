<?php

  $pTrue[$p+1] = $pFalse[$p+1] = '';
  $pPair[$p]   = TRUE;

  $pPos = $pEnd[$p];

go2:  
  do {

    $pPos = strpos($pHtml[$p] , '{/' . $pTag[$p], $pPos);

    if ($pPos === FALSE) {

      $pTrue[$p+1] = '';
      $pPair[$p]   = FALSE;

      return;
 
    } 

    $pTrue[$p+1] = substr($pHtml[$p], $pEnd[$p]+1, $pPos - $pEnd[$p] - 1);

    $pPos++;

  } while ( substrCnt($pTrue[$p+1], '{'.$pTag[$p]) <> substrCnt($pTrue[$p+1], '{/'.$pTag) );

  $pPair_check = substr($pHtml[$p], $pPos + strlen($pTag) + 1, 1);
  if ( ! ($pPair_check == ' ' or $pPair_check == '}' or $pPair_check ==  ',') )
    goto go2;
 
  $pTrue[$p+1] = substr ($pTrue[$p+1], 0, $pPos);

  $pEnd[$p] = strpos ( $pHtml[$p], '}', $pPos+2);
  if ( $pEnd[$p] === FALSE )
    return pError ("No closure of close tag found");

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
  $pWords   = preg_split ("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pPrms[$p]   = trim ($pWords[1] ?? '');

  if ($pPrms) {

    if ($pPrms) 
      return pError ("Both open and close parameters used: $pTag[$p]/ $pPrms[$p]/ $pPrms2");

    if ( strpos($pPrms2, '}') ) {
      pHtml ( '{close_parms}' . $pPrms[$p]. '{/close_parms}'
               . '{' . $pTag[$p]. '}'
               . $pTrue[$p+1]
               . '{/'. $pTag[$p]. ' ###%%%close_parms%%%###}') ;
      return TRUE;
    }


    $pad++;
    include 'between.php';
    $pPrmsType[$p] = 'close';
    $pad--;

  }

  $pOpen_close = [];
  $pOpen_close [$pTag[$p]] = TRUE;

  $pPos = strpos($pTrue[$p+1], '{/', 0);

  while ($pPos !== FALSE) {
    $pPos2 = strpos($pTrue[$p+1], '}', $pPos);
    if ( $pPos2 !== FALSE ) {
      $pPos3 = strpos($pTrue[$p+1], ' ', $pPos);
      if ($pPos3 !== FALSE and $pPos3 < $pPos2 )
        $pPos2 = $pPos3;      
      $pCheckTag = substr($pTrue[$p+1], $pPos+2, $pPos2-$pPos-2);
      if ( pValid ($pCheckTag) )
        $pOpen_close [$pCheckTag] = TRUE;
    }
    $pPos = strpos($pTrue[$p+1], '{/', $pPos+1);
  }

  $pPos = -1;

go: $pPos++;
    $pPos = strpos($pTrue[$p+1], '{else}', $pPos);

  if ( $pPos === FALSE )
    return TRUE;
  
  $pCheck = substr($pTrue[$p+1],0,$pPos);

  foreach ( $pOpen_close as $pCheckTag => $pDummy_var )
    if ( ( substrCnt($pCheck, '{'.$pCheckTag.' ' ) + substrCnt($pCheck, '{'.$pCheckTag.'}' ) )
           <> 
         ( substrCnt($pCheck, '{/'.$pCheckTag.' ') + substrCnt($pCheck, '{/'.$pCheckTag.'}') ) )
      goto go;

  $pFalse[$p+1] = substr ( $pTrue[$p+1], $pPos+6  );
  $pTrue[$p+1]  = substr ( $pTrue[$p+1], 0, $pPos );

  return TRUE;

?>