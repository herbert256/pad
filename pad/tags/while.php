<?php

  if ( $padWalk [$pad] == 'start' and $padPrmsType [$pad] == 'close' ) {
    $padWalk [$pad] = 'next';
    return TRUE;
  }

  $padEval = pEval ( $padPrms [$pad]);

  $padWrk  = [ $padTagCnt [$pad] => [] ];

  if ($padTag [$pad] == 'while') { 
    $padWalk [$pad] = ($padEval) ? 'next' : '';
    return        ($padEval) ? $padWrk  : NULL;
  } else {
    $padWalk [$pad] = ($padEval) ? ''   : 'next';
    return        ($padEval) ? NULL : $padWrk ;    
  }

?>