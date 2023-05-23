<?php

  if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
    $padWalk [$pad] = 'next';
    return TRUE;
  }

  $padEval = padEval ( $padOpt [$pad] [0] );

  if ($padTag [$pad] == 'while') { 
 
    $padWalk [$pad] = (   $padEval ) ? 'next' : '';
    return            (   $padEval ) ? [ 1 => [] ] : NULL;
 
  } else {
 
    $padWalk [$pad] = ( ! $padEval ) ? 'next' : '';
    return            ( ! $padEval ) ? [ 1 => [] ] : NULL;    
 
  }
 
?>