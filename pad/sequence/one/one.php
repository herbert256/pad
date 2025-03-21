<?php

  if ( $padPrmName == 'one' ) {
    $padSeqOneList = padExplode ( $padPrmValue, '|' );
    $padSeqOneName = $padSeqOneList [0] ?? '';  
    $padSeqOneParm = $padSeqOneList [1] ?? '';  
  } else {
    $padSeqOneName = $padPrmName;  
    $padSeqOneParm = $padPrmValue;      
  }

  if ( ! file_exists ( "sequence/one/types/$padSeqOneName.php" ) ) {
    $padSeqInfo ['errors'] [] = "no_one-$padSeqOneName-$padPage";
    return;
  }

  $padSeqNames [] = 'one';
  $padSeqNames [] = $padSeqOneName;

  $padSeqInfo ['one'] [] = $padSeqOneName;

  if ( count ( $padSeqResult ) ) 
    $padSeqResult = [ 1 => include "sequence/one/types/$padSeqOneName.php" ];

?>