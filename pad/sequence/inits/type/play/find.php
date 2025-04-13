<?php

  $padSeqTmp1    = '';
  $padSeqTmp2    = '';
  $padSeqTmpParm = $padSeqParm;

  if ( ! padSeqPlay ( $padSeqTag ) and ! padSeqPlay ($padSeqPrefix ) ) {

    $padSeqTmp1 = $padSeqPrefix;
    $padSeqTmp2 = $padSeqTag; 

  } else {

    $padSeqTmp1 = ( padSeqPlay ( $padSeqTag ) ) ? $padSeqPrefix : $padSeqTag; 

    if ( $padSeqFirst and file_exists ( "sequence/types/$padSeqFirst" ) ) {
      
      $padSeqTmp2    = $padSeqFirst; 
      $padSeqTmpParm = $padSeqFirstParm;
      
    } elseif ( $padSeqFirst and isset ( $padSeqStore [$padSeqFirst] ) ) {
  
      $padSeqTmp2    = $padSeqFirst; 
      $padSeqTmpParm = $padSeqFirstParm;

    } elseif ( $padSeqSecond and file_exists ( "sequence/types/$padSeqSecond" ) ) {
      
      $padSeqTmp2    = $padSeqSecond; 
      $padSeqTmpParm = $padSeqSecondParm;
      
    } elseif ( $padSeqSecond and isset ( $padSeqStore [$padSeqSecond] ) ) {
  
      $padSeqTmp2    = $padSeqSecond; 
      $padSeqTmpParm = $padSeqSecondParm;

    } 
 
  }

  if ( $padSeqTmp1 and $padSeqTmp2 ) {

    $padSeqPlay = $padSeqType;

    if ( isset ( $padSeqStore[$padSeqTmp1] ) and file_exists ( "sequence/types/$padSeqTmp2" ) ) {
      $padSeqPull    = $padSeqTmp1;
      $padSeqSeq     = $padSeqTmp2;
      $padSeqDone [] = $padSeqTmp2;
      $padSeqParm    = padSeqParm ( $padSeqTmpParm );
    } elseif ( isset ( $padSeqStore[$padSeqTmp2] ) and file_exists ( "sequence/types/$padSeqTmp1" ) ) {
      $padSeqPull    = $padSeqTmp2;
      $padSeqSeq     = $padSeqTmp1;
      $padSeqDone [] = $padSeqTmp2;
      $padSeqParm    = padSeqParm ( $padSeqTmpParm );
    } 

  }

?>