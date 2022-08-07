<?php


  $pTrue [$pad+1] = $pFalse [$pad+1] = '';
  $pPos = $pEnd [$pad];

go2:  
  do {

    $pPos = strpos($pHtml[$pad] , '{/' . $pTag, $pPos);

    if ($pPos === FALSE) {

      $pTrue [$pad+1] = '';
 
      $pParms [$pad] ['pair'] = FALSE;

      return TRUE;
 
    } 

    $pTrue [$pad+1] = substr($pHtml[$pad], $pEnd[$pad]+1, $pPos - $pEnd[$pad] - 1);

    $pPos++;

  } while ( substr_count($pTrue [$pad+1], '{'.$pTag ) <> substr_count($pTrue [$pad+1], '{/'.$pTag) );

  $pPair_check = substr($pHtml[$pad], $pPos + strlen($pTag) + 1, 1);
  if ( ! ($pPair_check == ' ' or $pPair_check == '}' or $pPair_check ==  ',') )
    goto go2;
 
  $pTrue [$pad+1] = substr ($pTrue [$pad+1], 0, $pPos);

  $pEnd [$pad] = strpos ( $pHtml[$pad], '}', $pPos+2);
  if ( $pEnd [$pad] === FALSE )
    return pError ("No closure of close tag found");

  $pTmp = substr ($pHtml[$pad], $pPos+1, $pEnd[$pad]-$pPos-1);

  while ( substr_count($pTmp, '{') <> substr_count($pTmp, '}') ) {

    if ( $pEnd [$pad] === FALSE or $pEnd [$pad] + 1 == strlen($pHtml[$pad]) )
       break;

    $pEnd [$pad] = strpos ( $pHtml[$pad], '}', $pEnd [$pad] + 1); 
    if ( $pEnd [$pad] !== FALSE )
      $pTmp = substr ($pHtml[$pad], $pPos+1, $pEnd[$pad]-$pPos-1);

  }

  if ( $pEnd [$pad] === FALSE )
    $pEnd [$pad] = strpos ( $pHtml[$pad], '}', $pPos+2);

  $pBetween = substr ($pHtml[$pad], $pPos+1, $pEnd[$pad]-$pPos-1);
  $pad_words   = preg_split ("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pPrms    = trim ($pad_words[1] ?? '');

  if ($pPrms) {

    if ($pPrms) 
      return pError ("Both open and close parameters used: $pTag / $pPrms / $pPrms2");

    if ( strpos($pPrms2, '}') ) {
      pHtml ( '{close_parms}' . $pPrms . '{/close_parms}'
               . '{' . $pTag . '}'
               . $pTrue [$pad+1]
               . '{/'. $pTag . ' ###%%%close_parms%%%###}') ;
      return TRUE;
    }


    $pad++;
    include 'between.php';
    $pParms [$pad] ['prms_type'] = 'close';
    $pad--;

  }

  $pOpen_close = [];
  $pOpen_close [$pTag] = TRUE;

  $pPos = strpos($pTrue [$pad+1], '{/', 0);

  while ($pPos !== FALSE) {
    $pPos2 = strpos($pTrue [$pad+1], '}', $pPos);
    if ( $pPos2 !== FALSE ) {
      $pPos3 = strpos($pTrue [$pad+1], ' ', $pPos);
      if ($pPos3 !== FALSE and $pPos3 < $pPos2 )
        $pPos2 = $pPos3;      
      $pCheck_tag = substr($pTrue [$pad+1], $pPos+2, $pPos2-$pPos-2);
      if ( pad_valid ($pCheck_tag) )
        $pOpen_close [$pCheck_tag] = TRUE;
    }
    $pPos = strpos($pTrue [$pad+1], '{/', $pPos+1);
  }

  $pPos = -1;

go: $pPos++;
    $pPos = strpos($pTrue [$pad+1], '{else}', $pPos);

  if ( $pPos === FALSE )
    return TRUE;
  
  $pCheck = substr($pTrue [$pad+1],0,$pPos);

  foreach ( $pOpen_close as $pCheck_tag => $pDummy_var )
    if ( ( substr_count($pCheck, '{'.$pCheck_tag.' ' ) + substr_count($pCheck, '{'.$pCheck_tag.'}' ) )
           <> 
         ( substr_count($pCheck, '{/'.$pCheck_tag.' ') + substr_count($pCheck, '{/'.$pCheck_tag.'}') ) )
      goto go;

  $pFalse   [$pad+1] = substr ( $pTrue [$pad+1], $pPos+6  );
  $pTrue [$pad+1] = substr ( $pTrue [$pad+1], 0, $pPos );

  return TRUE;

?>