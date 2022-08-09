<?php

  if ( $pWalk [$p] == 'start' ) {

    $pWalk [$p] = 'end';
    
    $pIsolate [$p] = [];
      
    foreach ($GLOBALS as $pK => $pV)
      if ( pValid_store ($pK) ) {
        $pIsolate [$p] [$pK] = TRUE;
        $pTmp = "pad_$p" . "_$pK";
        $$pTmp = $$pK;
      }
    
  } else {

    foreach ( $pIsolate [$p] as $pK => $pV ) {
      $pTmp = "pad_$p" . "_$pK";
      $$pK = $$pTmp;
    }
  
  }

  return TRUE;
    

?>