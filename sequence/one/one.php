<?php

  $padSeqOneList = padExplode ( $padPrmValue, '|' );
  $padSeqOneName = $padSeqOneList [0];  
  $padSeqOneParm = $padSeqOneList [1] ?? '';  

  $padSeqInfo ['one'] [] = $padSeqOneName;

  if ( count ( $padSeqResult ) ) 
    $padSeqResult = [ 1 => include "/pad/sequence/one/types/$padSeqOneName.php" ];

?>