<?php

  if ( padStartAndClose ('next') )
    return TRUE;

  $padWhile = padEval ( $padOpt [$pad] [0] );

  if ($padTag [$pad] == 'while') { 
 
    $padWalk [$pad] = (   $padWhile ) ? 'next' : '';
    return            (   $padWhile ) ? [ 1 => [] ] : NULL;
 
  } else {
 
    $padWalk [$pad] = ( ! $padWhile ) ? 'next' : '';
    return            ( ! $padWhile ) ? [ 1 => [] ] : NULL;    
 
  }
 
?>