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

  if ( $padFirst == '$' or $padFirst == '@' )
    $padOpts = array_merge ( $padDataDefaultStart, $padOpts, $padDataDefaultEnd );   

  if ( padTrace )
    include pad . 'info/events/var_start.php';   

  if ( substr($padFld, 0, 1) == '$' ) 
    $padFld = padFieldValue ( substr($padFld,1) );

  if  ( str_contains ( $field, '@' ) or $padFirst == '@' ) {
    if ( ! padValidVarAt ($padFld) ) 
      return padIgnore ( "Field '$padFld' is not a valid name" );
  }
  elseif ( $padFirst == '@' and ! padValidVarAt ($padFld) ) return padIgnore ( "Field '$padFld' is not a valid name" );
  elseif ( $padFirst <> '@' and ! padValidVar2  ($padFld) ) return padIgnore ( "Field '$padFld' is not a valid name" );

  if ( ! in_array('noError', $padOpts) ) {
    if  ( str_contains ( $field, '@' ) and ! padAtCheckField ( $padFld ) ) padError ( "Field '$padFld' not found" );
    elseif ( $padFirst == '!'          and ! padfieldCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" );
    elseif ( $padFirst == '?'          and ! padfieldCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" );
    elseif ( $padFirst == '$'          and ! padFieldCheck   ( $padFld ) ) padError ( "Field '$padFld' not found" );
    elseif ( $padFirst == '#'          and ! padOptCheck     ( $padFld ) ) padError ( "Field '$padFld' not found" );
    elseif ( $padFirst == '&'          and ! padTagCheck     ( $padFld ) ) padError ( "Field '$padFld' not found" );
    elseif ( $padFirst == '@'          and ! padAtCheckField ( $padFld ) ) padError ( "Field '$padFld' not found" );

  if     ( str_contains ( $field, '@'  ) $padVal = padAtValueField ($padFld);
  elseif ( $padFirst == '!'            ) $padVal = padRawValue     ($padFld);
  elseif ( $padFirst == '$'            ) $padVal = padFieldValue   ($padFld);
  elseif ( $padFirst == '#'            ) $padVal = padOptValue     ($padFld);
  elseif ( $padFirst == '&'            ) $padVal = padTagValue     ($padFld);
  elseif ( $padFirst == '?'            ) $padVal = padUrlValue     ($padFld);
  elseif ( $padFirst == '@'            ) $padVal = padAtValueField ($padFld);

  $padVal = padVarOpts ($padVal, $padOpts);

  if ( padTrace )
    include pad . 'info/events/var_end.php';   

  padPad ( $padVal );

?>