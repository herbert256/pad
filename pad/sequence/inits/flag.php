<?php

  $padSeqFlag = $padPrm [$pad] ['flag']   ?? ''; 

  padSplit ( '|', $padSeqFlag, $padSeqFlagSeq, $padSeqFlagParm );

  if ( $padSeqFlagSeq and file_exists ( "sequence/types/$padSeqFlagSeq/check.php" ) ) {

    include_once "sequence/types/$padSeqFlagSeq/check.php";

    if ( file_exists ( "sequence/types/$padSeqFlagSeq/bool.php") ) 
      include_once "sequence/types/$padSeqFlagSeq/bool.php";

    if ( file_exists ( "sequence/types/$padSeqFlagSeq/generated.php") ) 
      include_once "sequence/types/$padSeqFlagSeq/generated.php";
    
  } else {

    $padSeqFlagSeq = '';

    if ( file_exists ( "sequence/types/$padSeqSeq/check.php" ) ) {

      include_once "sequence/types/$padSeqSeq/check.php";

      if ( file_exists ( "sequence/types/$padSeqSeq/bool.php") ) 
        include_once "sequence/types/$padSeqSeq/bool.php";

      if ( file_exists ( "sequence/types/$padSeqSeq/generated.php") ) 
        include_once "sequence/types/$padSeqSeq/generated.php";
    
    }

  }

?>