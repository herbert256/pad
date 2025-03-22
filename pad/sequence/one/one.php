<?php

  if ( $padPrmName == 'one' ) {
    $padSeqOneList = padExplode ( $padPrmValue, '|' );
    $padSeqOneName = $padSeqOneList [0] ?? '';  
    $padSeqOneParm = $padSeqOneList [1] ?? '';  
  } else {
    $padSeqOneName = $padPrmName;  
    $padSeqOneParm = $padPrmValue;      
  }

  $padSeqNames [] = 'one';
  $padSeqNames [] = $padSeqOneName;

  if ( ! file_exists ( "sequence/one/types/$padSeqOneName.php" ) ) {
    $padSeqInfo ['errors'] [] = "$padPage-no_one-$padSeqOneName";
    return;
  }

  $padSeqInfo ['one'] [] = $padSeqOneName;

  if ( count ( $padSeqResult ) ) 
    $padSeqResult = [ 1 => include "sequence/one/types/$padSeqOneName.php" ];

?>