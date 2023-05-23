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

  if ( ! padValidVar2 ($padFld))
    return padIgnore ( "Field '$padFld' not a valid name" );

  if ( substr($padFld, 0, 1) == '$' ) {
    $padFld = padFieldValue ($padFld);
    if ( ! padValidVar2 ($padFld))
      return padIgnore ( "Field '$padFld' not a valid name" );
  }
 
  $padVarFound = $padVarSet = FALSE;
 
  if ( in_array('noError', $padOpts) )
    $padVarFound = TRUE;
  else
    if     ( $padFirst == '!' ) { if ( padRawCheck   ( $padFld ) ) $padVarFound = TRUE; }
    elseif ( $padFirst == '$' ) { if ( padFieldCheck ( $padFld ) ) $padVarFound = TRUE; }
    elseif ( $padFirst == '#' ) { if ( padParmCheck  ( $padFld ) ) $padVarFound = TRUE; }
    elseif ( $padFirst == '&' ) { if ( padTagCheck   ( $padFld ) ) $padVarFound = TRUE; }

  if ( ! $padVarFound and ( $padFirst == '$'  or $padFirst == '!' ) ) {

    $padVarDefined = get_defined_vars();

    if ( ! isset ( $padVarDefined [$padFld] ) )
      return padError ( "Field '$padFld' not found (1)" );  

    $padVal    = $padVarDefined [$padFld];
    $padValSet = TRUE;

  }

  if ( ! $padVarFound and ! $padVarDefined )
    return padError ( "Field '$padFld' not found (2)" ); 

  if ( ! $padVarSet )
    if     ( $padFirst == '!' ) $padVal = padRawValue   ($padFld);
    elseif ( $padFirst == '$' ) $padVal = padFieldValue ($padFld);
    elseif ( $padFirst == '#' ) $padVal = padParmValue  ($padFld);
    elseif ( $padFirst == '&' ) $padVal = padTagValue   ($padFld);

  if ( $padFirst == '$' )
    $padOpts = array_merge ( $padDataDefaultStart, $padOpts, $padDataDefaultEnd   );   

  return padHtml ( padVarOpts ($padVal, $padOpts) );

?>