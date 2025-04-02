<?php

  if ( ! file_exists ( "sequence/types/$padSeqSeq/init.php" ) ) 
    return;

  if ( $padSeqFlag ) {
    $padSeqFlagStart = $padSeqStart;
    $padSeqFlagInc   = $padSeqInc;
  }

  include "sequence/types/$padSeqSeq/init.php";

  if ( $padSeqFlag ) {
    $padSeqStart = $padSeqFlagStart;
    $padSeqInc   = $padSeqFlagInc;
  }

?>