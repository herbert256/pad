<?php

  if ( $padSeqStoreName )
     $padSeqStore [$padSeqStoreName] = $padSeqResult;

  if ( ! $padPair [$pad] and ! count ( $padSeqStoreList ) and ! $padSeqStoreName )
    $padSeqStore [$padSeqName] = $padSeqResult;

  if ( count ( $padSeqStoreList ) )
    include '/pad/sequence/store/exits.php';

?>