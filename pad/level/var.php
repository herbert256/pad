<?php

  // Renders the variable tag forms - {$x}, {!x}, {#x}, {&x}, {?x} - without a new level.
  //
  // Reached from level/level.php through try/try.php ($padTry = 'level/var'). The name runs
  // up to the first '|', anything after it is the pipe expression; a second '$' ({$$x})
  // means one round of indirection. The leading character picks the source: field, raw
  // (unescaped) field, tag option, tag property, or url parameter. A name that does not
  // exist is an error unless the pipe starts with 'optional'. Plain fields additionally run
  // through the $padDataDefaultStart and $padDataDefaultEnd chains from config (sanitize by
  // default), and padLevel() splices the value straight back into the surrounding text.

  $padPipe = strpos ( $padBetween, '|' );

  if ( $padPipe ) {
    $padFld  = rtrim(substr($padBetween, 1, $padPipe-1));
    $padVarOpts = trim(substr($padBetween, $padPipe+1));
  } else {
    $padFld  = rtrim(substr($padBetween, 1));
    $padVarOpts = '';
  }

  if ( substr($padFld, 0, 1) == '$' )
    $padFld = padFieldValue ( substr($padFld, 1) );

  if     ( $padFirst == '$' ) $padFldChk = padFieldCheck ( $padFld );
  elseif ( $padFirst == '?' ) $padFldChk = padFieldCheck ( $padFld );
  elseif ( $padFirst == '!' ) $padFldChk = padFieldCheck ( $padFld );
  elseif ( $padFirst == '#' ) $padFldChk = padOptCheck   ( $padFld );
  elseif ( $padFirst == '&' ) $padFldChk = padTagCheck   ( $padFld );
  elseif ( $padFirst == '^' ) $padFldChk = padFieldCheck ( $padFld );

  if ( ! $padFldChk and ! str_starts_with ( $padVarOpts, 'optional'))
    padError ( "Field '$padFirst$padFld' not found $padVarOpts" );

  if     ( $padFirst == '$' ) $padVal = padFieldValue ($padFld);
  elseif ( $padFirst == '?' ) $padVal = padUrlValue   ($padFld);
  elseif ( $padFirst == '!' ) $padVal = padRawValue   ($padFld);
  elseif ( $padFirst == '#' ) $padVal = padOptValue   ($padFld);
  elseif ( $padFirst == '&' ) $padVal = padTagValue   ($padFld);
  elseif ( $padFirst == '^' ) $padVal = padJsonEscape ($padFld);

  if ( $padFirst == '$' )
    foreach ( $padDataDefaultStart as $padOptOne )
      $padVal = padEval ( $padOptOne, $padVal );

  if ( $padVarOpts )
    $padVal = padEval ( $padVarOpts, $padVal );

  if ( $padFirst == '$' )
    foreach ( $padDataDefaultEnd as $padOptOne )
      $padVal = padEval ( $padOptOne, $padVal );

  padLevel ( $padVal );

?>