<?php

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {
    $pad_walk = 'next';
    return TRUE;
  }

  $pad_eval = pad_eval ( $pad_parms );

  $pad_wrk  = [ $pad_parameters [$pad_lvl] ['tag_count'] => [] ];

  if ($pad_tag == 'while') { 
    $pad_walk = ($pad_eval) ? 'next'   : '';
    return      ($pad_eval) ? $pad_wrk : NULL;
  } else {
    $pad_walk = ($pad_eval) ? ''   : 'next';
    return      ($pad_eval) ? NULL : $pad_wrk;    
  }

?>