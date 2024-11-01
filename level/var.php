<?php
  
  $padPipe = strpos ( $padBetween, '|' );

  if ( $padPipe ) {
    $padFld  = rtrim(substr($padBetween, 1, $padPipe-1));
    $padOpts = trim(substr($padBetween, $padPipe+1));
  } else {
    $padFld  = rtrim(substr($padBetween, 1));
    $padOpts = '';
  }
 
  if ( substr($padFld, 0, 1) == '$' ) 
    $padFld = padFieldValue ( substr($padFld, 1) );

  if     ( $padFirst == '$' ) $padFldChk = padFieldCheck ( $padFld );
  elseif ( $padFirst == '?' ) $padFldChk = padfieldCheck ( $padFld );
  elseif ( $padFirst == '!' ) $padFldChk = padfieldCheck ( $padFld );
  elseif ( $padFirst == '#' ) $padFldChk = padOptCheck   ( $padFld );
  elseif ( $padFirst == '&' ) $padFldChk = padTagCheck   ( $padFld );

  if ( ! $padFldChk and ! str_starts_with ( $padOpts, 'optional'))
    padError ( "Field '$padFld' not found $padOpts" );

  if     ( $padFirst == '$' ) $padVal = padFieldValue ($padFld);
  elseif ( $padFirst == '?' ) $padVal = padUrlValue   ($padFld);
  elseif ( $padFirst == '!' ) $padVal = padRawValue   ($padFld);
  elseif ( $padFirst == '#' ) $padVal = padOptValue   ($padFld);
  elseif ( $padFirst == '&' ) $padVal = padTagValue   ($padFld);

  if ( $padFirst == '$' ) 
    foreach ( $padDataDefaultStart as $padOptOne )
      $padVal = padEval ( $padOptOne, $padVal );

  if ( $padOpts )
    $padVal = padEval ( $padOpts, $padVal );

  if ( $padFirst == '$' ) 
    foreach ( $padDataDefaultEnd as $padOptOne )
      $padVal = padEval ( $padOptOne, $padVal );

  padPad ( $padVal );

?>