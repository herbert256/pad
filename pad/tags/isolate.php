<?php

  if ( $pWalk[$p] == 'start' ) {

    $pWalk[$p] = 'end';
    
    $pIsolate[$p] = [];
      
    foreach ($GLOBALS as $pK => $pV)
      if ( pValid_store ($pK) ) {
        $pIsolate[$p] [$pK] = TRUE;
        $pTmp = "pad_$pad" . "_$pK";
        $$pTmp = $$pK;
      }
    
  } else {

    foreach ( $pIsolate[$p] as $pK => $pV ) {
      $pTmp = "pad_$pad" . "_$pK";
      $$pK = $$pTmp;
    }
  
  }

  return TRUE;
    

?>