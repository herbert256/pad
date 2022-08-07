<?php

  if ( $pad_walk == 'start' and $pPrmsType == 'close' ) {
    $pad_walk = 'next';
    return TRUE;
  }

  $pEval = pEval ( $pPrms );

  $pad_wrk  = [ $TagCnt[$p] => [] ];

  if ($pTag == 'while') { 
    $pad_walk = ($pEval) ? 'next'   : '';
    return      ($pEval) ? $pad_wrk : NULL;
  } else {
    $pad_walk = ($pEval) ? ''   : 'next';
    return      ($pEval) ? $pad_wrk : TRUE;    
  }

?>