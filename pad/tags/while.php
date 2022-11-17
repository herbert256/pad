<?php

  if ( $padWalk [$pad] == 'start' and $padPrmsType [$pad] == 'close' ) {
    $padWalk [$pad] = 'next';
    return TRUE;
  }

  $padEval = padEval ( $padPrms [$pad] );

  if ($padTag [$pad] == 'while') { 
    $padWalk [$pad] = ($padEval) ? 'next' : '';
    return ($padEval) ? [ 1 => [] ] : NULL;
  } else {
    $padWalk [$pad] = ($padEval) ? ''   : 'next';
    return ($padEval) ? NULL : [ 1 => [] ];    
  }
 
?>