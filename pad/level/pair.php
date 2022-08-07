<?php


  $pTrue [$p+1] = $pFalse [$p+1] = '';
  $pPos = $pEnd [$p];

go2:  
  do {

    $pPos = strpos($pHtml[$p] , '{/' . $pTag, $pPos);

    if ($pPos === FALSE) {

      $pTrue [$p+1] = '';
 
      $pPair [$p] = FALSE;

      return TRUE;
 
    } 

    $pTrue [$p+1] = substr($pHtml[$p], $pEnd[$p]+1, $pPos - $pEnd[$p] - 1);

    $pPos++;

  } while ( substr_count($pTrue [$p+1], '{'.$pTag ) <> substr_count($pTrue [$p+1], '{/'.$pTag) );

  $pPair_check = substr($pHtml[$p], $pPos + strlen($pTag) + 1, 1);
  if ( ! ($pPair_check == ' ' or $pPair_check == '}' or $pPair_check ==  ',') )
    goto go2;
 
  $pTrue [$p+1] = substr ($pTrue [$p+1], 0, $pPos);

  $pEnd [$p] = strpos ( $pHtml[$p], '}', $pPos+2);
  if ( $pEnd [$p] === FALSE )
    return pError ("No closure of close tag found");

  $pTmp = substr ($pHtml[$p], $pPos+1, $pEnd[$p]-$pPos-1);

  while ( substr_count($pTmp, '{') <> substr_count($pTmp, '}') ) {

    if ( $pEnd [$p] === FALSE or $pEnd [$p] + 1 == strlen($pHtml[$p]) )
       break;

    $pEnd [$p] = strpos ( $pHtml[$p], '}', $pEnd [$p] + 1); 
    if ( $pEnd [$p] !== FALSE )
      $pTmp = substr ($pHtml[$p], $pPos+1, $pEnd[$p]-$pPos-1);

  }

  if ( $pEnd [$p] === FALSE )
    $pEnd [$p] = strpos ( $pHtml[$p], '}', $pPos+2);

  $pBetween = substr ($pHtml[$p], $pPos+1, $pEnd[$p]-$pPos-1);
  $pad_words   = preg_split ("/[\s]+/", $pBetween, 2, PREG_SPLIT_NO_EMPTY);
  $pPrms    = trim ($pad_words[1] ?? '');

  if ($pPrms) {

    if ($pPrms) 
      return pError ("Both open and close parameters used: $pTag / $pPrms / $pPrms2");

    if ( strpos($pPrms2, '}') ) {
      pHtml ( '{close_parms}' . $pPrms . '{/close_parms}'
               . '{' . $pTag . '}'
               . $pTrue [$p+1]
               . '{/'. $pTag . ' ###%%%close_parms%%%###}') ;
      return TRUE;
    }


    $pad++;
    include 'between.php';
    $pPrmsType [$p] = 'close';
    $pad--;

  }

  $pOpen_close = [];
  $pOpen_close [$pTag] = TRUE;

  $pPos = strpos($pTrue [$p+1], '{/', 0);

  while ($pPos !== FALSE) {
    $pPos2 = strpos($pTrue [$p+1], '}', $pPos);
    if ( $pPos2 !== FALSE ) {
      $pPos3 = strpos($pTrue [$p+1], ' ', $pPos);
      if ($pPos3 !== FALSE and $pPos3 < $pPos2 )
        $pPos2 = $pPos3;      
      $pCheck_tag = substr($pTrue [$p+1], $pPos+2, $pPos2-$pPos-2);
      if ( pad_valid ($pCheck_tag) )
        $pOpen_close [$pCheck_tag] = TRUE;
    }
    $pPos = strpos($pTrue [$p+1], '{/', $pPos+1);
  }

  $pPos = -1;

go: $pPos++;
    $pPos = strpos($pTrue [$p+1], '{else}', $pPos);

  if ( $pPos === FALSE )
    return TRUE;
  
  $pCheck = substr($pTrue [$p+1],0,$pPos);

  foreach ( $pOpen_close as $pCheck_tag => $pDummy_var )
    if ( ( substr_count($pCheck, '{'.$pCheck_tag.' ' ) + substr_count($pCheck, '{'.$pCheck_tag.'}' ) )
           <> 
         ( substr_count($pCheck, '{/'.$pCheck_tag.' ') + substr_count($pCheck, '{/'.$pCheck_tag.'}') ) )
      goto go;

  $pFalse   [$p+1] = substr ( $pTrue [$p+1], $pPos+6  );
  $pTrue [$p+1] = substr ( $pTrue [$p+1], 0, $pPos );

  return TRUE;

?>