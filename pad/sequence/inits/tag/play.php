<?php

  // {make mySequence, add, 3 }
  // {make add, mysequence, 3 }

  if ( $padSeqFirst and $padSeqSecond )
    if       ( isset ( $padSeqStore[$padSeqFirst] ) and file_exists ( "sequence/types/$padSeqSecond" ) ) {

      $padSeqPull = $padSeqFirst;
      $padSeqSeq  = $padSeqSecond;

      if ( $padSeqSecondParm and $padSeqSecondParm !== TRUE and ! $padSeqParm )
        $padSeqParm = $padSeqSecondParm;
    
    } elseif ( isset ( $padSeqStore[$padSeqSecond] ) and file_exists ( "sequence/types/$padSeqFirst" ) ) {
    
      $padSeqPull = $padSeqSecond;
      $padSeqSeq  = $padSeqFirst;
    
      if ( $padSeqFirstParm and $padSeqFirstParm !== TRUE and ! $padSeqParm )
        $padSeqParm = $padSeqFirstParm;
    
    }
 
  if ( $padSeqPull ) {
 
    $padSeqDone [] = $padSeqFirst;
    $padSeqDone [] = $padSeqSecond;
 
    $padSeqPlay  = $padSeqTag;

  }

?>