<?php

  $padSeqParmSave = $padSeqParm;

  foreach ( $GLOBALS ["padSeq_mf_$padSeqSeq"] as $padSeqOptName => $padSeqOptValue ) {

    $padSeqOneDone [] = $padSeqOptName;

    $padSeqParm = $padSeqOptValue;

    $padSeqOptCheck = include PAD . "sequence/types/$padSeqOptName/$padSeqFilterCheck.php"; 

    if ( $padSeqSeq == 'keep') { 
    
      if ( $padSeqOptCheck === FALSE ) {
        $padSeqParm = $padSeqParmSave;
        return FALSE;
      }
    
    } elseif ( $padSeqSeq == 'remove') { 
    
      if ( $padSeqOptCheck === TRUE ) {
        $padSeqParm = $padSeqParmSave;
        return FALSE;
      }
    
    } else {

      $padSeqLoop = $padSeqOptCheck;
    
    }

  }

  $padSeqParm = $padSeqParmSave;

  return $padSeqLoop;

?>