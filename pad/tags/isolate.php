<?php

  if ( $pad_walk == 'start' ) {

    $pad_walk = 'end';
    
    $pIsolate [$pad] = [];
      
    foreach ($GLOBALS as $pK => $pad_v)
      if ( pad_valid_store ($pK) ) {
        $pIsolate [$pad] [$pK] = TRUE;
        $pTmp = "pad_$pad" . "_$pK";
        $$pTmp = $$pK;
      }
    
  } else {

    foreach ( $pIsolate [$pad] as $pK => $pad_v ) {
      $pTmp = "pad_$pad" . "_$pK";
      $$pK = $$pTmp;
    }
  
  }

  return TRUE;
    

?>