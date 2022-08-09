<?php

  if ( $pWalk [$p] == 'start' and $pPrmsType [$p] == 'close' ) {
    $pWalk [$p] = 'next';
    return TRUE;
  }

  $pEval = pEval ( $pPrms [$p]);

  $pWrk  = [ $pTagCnt [$p] => [] ];

  if ($pTag [$p] == 'while') { 
    $pWalk [$p] = ($pEval) ? 'next'   : '';
    return      ($pEval) ? $pWrk : NULL;
  } else {
    $pWalk [$p] = ($pEval) ? ''   : 'next';
    return      ($pEval) ? $pWrk : TRUE;    
  }

?>