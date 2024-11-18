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

  $padSeqInfo ['one'] [] = $padSeqOneName;

  if ( count ( $padSeqResult ) ) 
    $padSeqResult = [ 1 => include PAD . "seq/one/types/$padSeqOneName.php" ];

?>