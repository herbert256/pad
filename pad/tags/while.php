<?php

  if ( $pad_walk == 'start' and $pPrms_type == 'close' ) {
    $pad_walk = 'next';
    return TRUE;
  }

  $pEval = pEval ( $pPrms );

  $pad_wrk  = [ $pParms [$p] ['tag_cnt'] => [] ];

  if ($pTag == 'while') { 
    $pad_walk = ($pEval) ? 'next'   : '';
    return      ($pEval) ? $pad_wrk : NULL;
  } else {
    $pad_walk = ($pEval) ? ''   : 'next';
    return      ($pEval) ? $pad_wrk : TRUE;    
  }

?>