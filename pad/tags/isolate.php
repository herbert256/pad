<?php

  if ( $padWalk [$pad] == 'start' ) {

    $padWalk [$pad] = 'end';
    
    $padIsolate [$pad] = [];
      
    foreach ($GLOBALS as $padK => $padV)
      if ( padValidStore ($padK) ) {
        $padIsolate [$pad] [$padK] = TRUE;
        $padTmp = "pad_$pad" . "_$padK";
        $$padTmp = $$padK;
      }
    
  } else {

    foreach ( $padIsolate [$pad] as $padK => $padV ) {
      $padTmp = "pad_$pad" . "_$padK";
      $$padK = $$padTmp;
    }
  
  }

  return TRUE;
    

?>