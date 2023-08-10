<?php

  $padSeqMkrSeq = $padSequence;
  $padSeqMkrLst = $padSequence;

  foreach ( $padSeqMkrGo as $padSeqMkrType => $padSeqMkrTmp ) 

    foreach ( $padSeqMkrTmp as $padSeqMkrName => $padSeqParm ) {

      $padSeqLoop = $padSeqMkrSeq;

      if ( $padSeqMkrType == 'make')
        $padSeqMkrSeq = include "$padSeqTypes/$padSeqMkrName/make.php"; 
      else
        $padSeqMkrSeq = include "$padSeqTypes/$padSeqMkrName/filter.php"; 

      if   (  ( $padSeqMkrSeq === TRUE  and $padSeqMkrType == 'remove' ) or 
              ( $padSeqMkrSeq === FALSE and $padSeqMkrType == 'keep'   ) )   
        return TRUE;

      if   (  ( $padSeqMkrSeq === FALSE and $padSeqMkrType == 'remove' ) or 
              ( $padSeqMkrSeq === TRUE  and $padSeqMkrType == 'keep'   ) )   
        $padSeqMkrSeq = $padSeqMkrLst;

      $padSeqMkrLst = $padSeqMkrSeq;

    }

  return $padSeqMkrSeq;

?>