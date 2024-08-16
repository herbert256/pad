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

  if ( str_contains ( $padFld, '.' ) and ! str_contains ( $padFld, '@' ) )
    $padFld .= '@*';

  if ( $padFirst == '@' ) {
    $padFirst = '$';
    if ( ! str_contains ( $padFld, '@' ) )
      $padFld .= '@*';
  }
 
  if ( substr($padFld, 0, 1) == '$' ) 
    $padFld = padFieldValue ( substr($padFld, 1) );

  if ( ! padValidVar2 ($padFld) ) 
    return padError ( "Field '$padFld' is not a valid name" );

  if     ( $padFirst == '$' ) $padFldChk = padFieldCheck ( $padFld );
  elseif ( $padFirst == '?' ) $padFldChk = padfieldCheck ( $padFld );
  elseif ( $padFirst == '!' ) $padFldChk = padfieldCheck ( $padFld );
  elseif ( $padFirst == '#' ) $padFldChk = padOptCheck   ( $padFld );
  elseif ( $padFirst == '&' ) $padFldChk = padTagCheck   ( $padFld );

  if ( $padSyntax and ! $padFldChk and ! in_array ( 'optional', $padOpts ) )
    padError ( "Field '$padFld' not found" );

  if ( ! $padSyntax and ! $padFldChk )
    return padPad ( $padSyntaxDefaultVar );

  if     ( $padFirst == '$' ) $padVal = padFieldValue ($padFld);
  elseif ( $padFirst == '?' ) $padVal = padUrlValue   ($padFld);
  elseif ( $padFirst == '!' ) $padVal = padRawValue   ($padFld);
  elseif ( $padFirst == '#' ) $padVal = padOptValue   ($padFld);
  elseif ( $padFirst == '&' ) $padVal = padTagValue   ($padFld);

  if ( $padFirst == '$' )
    $padOpts = array_merge ( $padDataDefaultStart, $padOpts, $padDataDefaultEnd );   

  $padVal = padVarOpts ($padVal, $padOpts);

  padPad ( $padVal );

?>