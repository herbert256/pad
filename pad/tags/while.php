<?php

  if ( $pWalk == 'start' and $pPrmsType == 'close' ) {
    $pWalk = 'next';
    return TRUE;
  }

  $pEval = pEval ( $pPrms[$p]);

  $pWrk  = [ $TagCnt[$p] => [] ];

  if ($pTag[$p]== 'while') { 
    $pWalk = ($pEval) ? 'next'   : '';
    return      ($pEval) ? $pWrk : NULL;
  } else {
    $pWalk = ($pEval) ? ''   : 'next';
    return      ($pEval) ? $pWrk : TRUE;    
  }

?>