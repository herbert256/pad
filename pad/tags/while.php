<?php

  if ( padStartAndClose ('next') )
    return TRUE;

  $padWhile = padEval ( $padParms [$pad] [0] ['padPrmOrg'] );

  if ($padTag [$pad] == 'while') { 
 
    $padWalk [$pad] = (   $padWhile ) ? 'next' : '';
    return            (   $padWhile ) ? [ 1 => [] ] : NULL;
 
  } else {
 
    $padWalk [$pad] = ( ! $padWhile ) ? 'next' : '';
    return            ( ! $padWhile ) ? [ 1 => [] ] : NULL;    
 
  }
 
?>