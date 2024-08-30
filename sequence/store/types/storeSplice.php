<?php

   $padSeqActionList     = padExplode ( $padSeqStoreValue, '|' );
   $padSeqActionList [1] = $padSeqActionList [1] ?? NULL;
   $padSeqActionList [2] = $padSeqStoreName; 

   include '/pad/sequence/store/switch.php';

?>