<?php

  if ( $padSeqStoreName ) 
     $padSeqStore [$padSeqStoreName] = array_values ( $padSeqResult );

  if ( ! $padPair [$pad] and ! count ( $padSeqStoreList ) and ! $padSeqStoreName )
    $padSeqStore [$padSeqName] = array_values ( $padSeqResult );

  if ( count ( $padSeqStoreList ) )
    include '/pad/sequence/store/store.php';
   
?>