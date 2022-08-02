<?php

  if ( $pad_walk == 'start' and $pad_parms_type == 'close' ) {
    $pad_walk = 'next';
    return TRUE;
  }

  $pad_eval = pad_eval ( $pad_parms );

  if ($pad_tag == 'while') { 
    $pad_walk = ($pad_eval) ? 'next'   : '';
    return      ($pad_eval) ? TRUE : NULL;
  } else {
    $pad_walk = ($pad_eval) ? ''   : 'next';
    return      ($pad_eval) ? TRUE : $pad_wrk;    
  }

?>