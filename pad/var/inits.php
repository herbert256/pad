<?php

  $padFldCnt++;

  $padPipe    = strpos ( $padBetween, '|' );
  $padSpace   = strpos ( $padBetween, ' ' );

  if ($padPipe and (!$padSpace or $padPipe < $padSpace) ) {
    
    $padFld  = rtrim(substr($padBetween, 1, $padPipe-1));
    $padExpl = pExplode(substr($padBetween, $padPipe+1), '|');

  } elseif ($padSpace and (!$padPipe or $padSpace < $padPipe) ) {

    $padFld  = rtrim(substr($padBetween, 1, $padSpace-1));
    $padExpl = pExplode(substr($padBetween, $padSpace+1), '|');

  } else {

    $padFld  = rtrim(substr($padBetween, 1));
    $padExpl = [];

  }
  
  if ( substr($padFld, 0, 1) == '$' )
    $padFld = pField_value ($padFld);

  $padVal = pField_value ($padFld);

  $padVal_base = $padVal;

  if ( ! pField_check ( $padFld ) ) 
      pError ( "Field '$padFld' not found" )

?>