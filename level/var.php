<?php

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

  if ( substr($padFld, 0, 1) == '$' ) {
    $padFld = padFieldValue ($padFld);
  }

  if ( ! padValidVar2 ($padFld))
    return padIgnore ( "Field '$padFld' not a valid name" );
 
  if ( ! in_array('noError', $padOpts) )
    if     ( $padFirst == '!' ) { if ( ! padRawCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" ); }
    elseif ( $padFirst == '$' ) { if ( ! padFieldCheck ( $padFld ) ) padError ( "Field '$padFld' not found" ); }
    elseif ( $padFirst == '#' ) { if ( ! padParmCheck  ( $padFld ) ) padError ( "Field '$padFld' not found" ); }
    elseif ( $padFirst == '&' ) { if ( ! padTagCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" ); }

  if     ( $padFirst == '!' ) $padVal = padRawValue   ($padFld);
  elseif ( $padFirst == '$' ) $padVal = padFieldValue ($padFld);
  elseif ( $padFirst == '#' ) $padVal = padParmValue  ($padFld);
  elseif ( $padFirst == '&' ) $padVal = padTagValue   ($padFld);

  if ( $padFirst == '$' )
    $padOpts = array_merge ( $padDataDefaultStart, $padOpts, $padDataDefaultEnd   );   

  padHtml ( padVarOpts ($padVal, $padOpts) );

  $padBusy = '';

  return;

?>