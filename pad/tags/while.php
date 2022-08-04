<?php

  if ( $pad_walk == 'start' and $pad_prms_type == 'close' ) {
    $pad_walk = 'next';
    return TRUE;
  }

  $pad_eval = pad_eval ( $pad_prms );

  $pad_wrk  = [ $pad_parms [$pad] ['tag_cnt'] => [] ];

  if ($pad_tag == 'while') { 
    $pad_walk = ($pad_eval) ? 'next'   : '';
    return      ($pad_eval) ? $pad_wrk : NULL;
  } else {
    $pad_walk = ($pad_eval) ? ''   : 'next';
    return      ($pad_eval) ? $pad_wrk : TRUE;    
  }

?>