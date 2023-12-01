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
    include pad . 'tail/events/var_start.php';   

  if ( substr($padFld, 0, 1) == '$' ) 
    $padFld = padFieldValue ( substr($padFld,1) );

  if ( str_contains ( $padFld, '.' ) or str_contains ( $padFld, '@' ) )
    $padVal = include pad . 'var/at.php';
  else
    $padVal = include pad . 'var/classic.php';

  if ( $padFirst == '$' )
    $padOpts = array_merge ( $padDataDefaultStart, $padOpts, $padDataDefaultEnd );   

  $padVal = padVarOpts ($padVal, $padOpts);

  if ( padTrace )
    include pad . 'tail/events/var_end.php';   

  padPad ( $padVal );

?>