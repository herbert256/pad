<?php

  if ( $pad_parms_type == 'close' and $pad_walk == 'start' ) {
    $pad_walk = 'next';
    return TRUE;
  }

  $pad_eval = $pad_parms_seq[0];
  
  $pad_wrk  = [ $pad_parameters [$pad_lvl] ['tag_count'] => [] ];

  if ($pad_tag == 'while') { 
    $pad_walk = ($pad_eval) ? 'next'   : '';
    return      ($pad_eval) ? $pad_wrk : NULL;
  } else {
    $pad_walk = ($pad_eval) ? ''   : 'next';
    return      ($pad_eval) ? NULL : $pad_wrk;    
  }

?>