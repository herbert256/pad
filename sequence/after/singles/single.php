<?php

  $padSeqSingleList = padExplode ( $padPrmValue, '|' );
  $padSeqSingleName = $padSeqSingleList [0];  

  $padSeqInfo ['singles'] [] = $padSeqSingleName;

  if ( count ( $padSeqResult ) ) 
    $padSeqResult = [ 1 => include "/pad/sequence/after/singles/types/$padSeqSingleName.php" ];

?>