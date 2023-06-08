<?php

  $padBetween = ( str_starts_with ( $padBetween, '$' ) ) ? substr ( $padBetween, 1 ) : $padBetween;

  $padPipe  = strpos ( $padBetween, '|' );
  $padSpace = strpos ( $padBetween, ' ' );

  if ($padPipe and (!$padSpace or $padPipe < $padSpace) ) {
    
    $padFld  = rtrim(substr($padBetween, 0, $padPipe-1));
    $padOpts = padExplode(substr($padBetween, $padPipe+1), '|');

  } elseif ($padSpace and (!$padPipe or $padSpace < $padPipe) ) {

    $padFld  = rtrim(substr($padBetween, 0, $padSpace-1));
    $padOpts = padExplode(substr($padBetween, $padSpace+1), '|');

  } else {

    $padFld  = rtrim($padBetween);
    $padOpts = [];

  }

  if ( ! padValidVarAt ($padFld))
    return padIgnore ( "Field '$padFld' not a valid name" );

  $padVal = padAt ($padFld);

  if ( ! in_array('noError', $padOpts) and $padVal === INF )
    padError ("Field '$padFld' not found");

  if ( ! in_array('raw', $padOpts) )
    $padOpts = array_merge ( $padDataDefaultStart, $padOpts, $padDataDefaultEnd   );   

  padHtml ( padVarOpts ($padVal, $padOpts) );

  $padBusy = '';

  return;

?>