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

  if ( padTrace )
    include pad . 'info/events/var_start.php';   

  if ( substr($padFld, 0, 1) == '$' ) 
    $padFld = padFieldValue ( substr($padFld,1) );

  if ( $padFirst == '@' and ! padValidVarAt ($padFld) ) return padIgnore ( "Field '$padFld' is not a valid name" );
  if ( $padFirst <> '@' and ! padValidVar2  ($padFld) ) return padIgnore ( "Field '$padFld' is not a valid name" );

  if ( ! in_array('noError', $padOpts) )
    if     ( $padFirst == '!' ) { if ( ! padfieldCheck ( $padFld ) ) padError ( "Field '$padFld' not found" ); }
    elseif ( $padFirst == '$' ) { if ( ! padFieldCheck ( $padFld ) ) padError ( "Field '$padFld' not found" ); }
    elseif ( $padFirst == '#' ) { if ( ! padOptCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" ); }
    elseif ( $padFirst == '&' ) { if ( ! padTagCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" ); }
    elseif ( $padFirst == '@' ) { if ( ! padAtCheck    ( $padFld ) ) padError ( "Field '$padFld' not found" ); }

  if     ( $padFirst == '!' ) $padVal = padRawValue   ($padFld);
  elseif ( $padFirst == '$' ) $padVal = padFieldValue ($padFld);
  elseif ( $padFirst == '#' ) $padVal = padOptValue   ($padFld);
  elseif ( $padFirst == '&' ) $padVal = padTagValue   ($padFld);
  elseif ( $padFirst == '?' ) $padVal = padUrlValue   ($padFld);
  elseif ( $padFirst == '@' ) $padVal = padAtValue    ($padFld);

  if ( $padFirst == '$' )
    $padOpts = array_merge ( $padDataDefaultStart, $padOpts, $padDataDefaultEnd );   

  $padVal = padVarOpts ($padVal, $padOpts);

  if ( padTrace )
    include pad . 'info/events/var_end.php';   

  padPad ( $padVal );

?>