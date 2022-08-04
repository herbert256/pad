<?php

  if ( $pad_walk == 'start' ) {

    $pad_walk = 'end';
    
    $pad_isolate [$pad] = [];
      
    foreach ($GLOBALS as $pad_k => $pad_v)
      if ( pad_valid_store ($pad_k) ) {
        $pad_isolate [$pad] [$pad_k] = TRUE;
        $pad_tmp = "pad_$pad" . "_$pad_k";
        $$pad_tmp = $$pad_k;
      }
    
  } else {

    foreach ( $pad_isolate [$pad] as $pad_k => $pad_v ) {
      $pad_tmp = "pad_$pad" . "_$pad_k";
      $$pad_k = $$pad_tmp;
    }
  
  }

  return TRUE;
    

?>