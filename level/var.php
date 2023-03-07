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

  $padFirst   = substr ( $padBetween , 0, 1 );

  if ( $padParse ) 
    return include PAD . 'parse/var.php';
 
  if     ( $padFirst == '!' ) if ( ! padFieldCheck ( $padFld ) ) padError ( "Field '$padFld' not found" );
  elseif ( $padFirst == '$' ) if ( ! padFieldCheck ( $padFld ) ) padError ( "Field '$padFld' not found" );
  elseif ( $padFirst == '#' ) if ( ! padParmCheck  ( $padFld ) ) padError ( "Field '$padFld' not found" );
  elseif ( $padFirst == '&' ) if ( ! padTagCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" );

  if     ( $padFirst == '!' ) $padVal = str_replace ( '}', '&close;', padFieldValue ($padFld) );
  elseif ( $padFirst == '$' ) $padVal = padFieldValue ($padFld);
  elseif ( $padFirst == '#' ) $padVal = padParmValue  ($padFld);
  elseif ( $padFirst == '&' ) $padVal = padTagValue   ($padFld);

  if ( $padFirst == '$' ) {
    $padOpts = array_merge ( $padDataDefaultStart, $padOpts );
    $padOpts = array_merge ( $padOpts, $padDataDefaultEnd   );   
  }

  $padValBase = $padVal;
  $padVal = padVarOpts ($padVal, $padOpts);

  if ( $padTrace ) 
    include 'trace/var.php';

  if ( $padLog ) 
    include PAD . 'log/var.php';
 
  padTimingEnd ('var');

  return padHtml ( $padVal );

?>