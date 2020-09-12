<?php

  if ( $pad_walk == 'start' ) {

    $pad_walk = 'end';
    
    $pad_isolate [$pad_lvl] = [];
      
    foreach ($GLOBALS as $pad_k => $pad_v)
      if ( pad_valid_store ($pad_k) ) {
        $pad_isolate [$pad_lvl] [$pad_k] = TRUE;
        $pad_tmp = "pad_$pad_lvl" . "_$pad_k";
        $$pad_tmp = $$pad_k;
      }
    
  } else {

    foreach ( $pad_isolate [$pad_lvl] as $pad_k => $pad_v ) {
      $pad_tmp = "pad_$pad_lvl" . "_$pad_k";
      $$pad_k = $$pad_tmp;
    }
  
  }

  return TRUE;
    

?>