<?php

  $padFldCnt++;

  $padPipe    = strpos ( $padBetween, '|' );
  $padSpace   = strpos ( $padBetween, ' ' );

  if ($padPipe and (!$padSpace or $padPipe < $padSpace) ) {
    
    $padFld  = rtrim(substr($padBetween, 1, $padPipe-1));
    $padExpl = padExplode(substr($padBetween, $padPipe+1), '|');

  } elseif ($padSpace and (!$padPipe or $padSpace < $padPipe) ) {

    $padFld  = rtrim(substr($padBetween, 1, $padSpace-1));
    $padExpl = padExplode(substr($padBetween, $padSpace+1), '|');

  } else {

    $padFld  = rtrim(substr($padBetween, 1));
    $padExpl = [];

  }
  
  if ( substr($padFld, 0, 1) == '$' )
    $padFld = padFieldValue ($padFld);

  $padVal = padFieldValue ($padFld);

  $padVal_base = $padVal;

  if ( ! padFieldCheck ( $padFld ) ) 
      padError ( "Field '$padFld' not found" )

?>