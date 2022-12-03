<?php

  padTimingStart ('var');

  $padFldCnt++;

  $padPipe  = strpos ( $padBetween, '|' );
  $padSpace = strpos ( $padBetween, ' ' );

  if ($padPipe and (!$padSpace or $padPipe < $padSpace) ) {
    
    $padFld  = rtrim(substr($padBetween, 1, $padPipe-1));
    $padOpts = padExplode(substr($padBetween, $padPipe+1), '|');

  } elseif ($padSpace and (!$padPipe or $padSpace < $padPipe) ) {

    $padFld  = rtrim(substr($padBetween, 1, $padSpace-1));
    $padOpts = padExplode(substr($padBetween, $padSpace+1), '|');

  } else {

    $padFld  = rtrim(substr($padBetween, 1));
    $padOpts = [];

  }
  
  if ( substr($padFld, 0, 1) == '$' )
    $padFld = padFieldValue ($padFld);

  $padVal = padFieldValue ($padFld);

  $padValBase = $padVal;

  if ( ! padFieldCheck ( $padFld ) ) 
    padError ( "Field '$padFld' not found" )

?>