<?php

  if ( $padPair [$pad] and ! count ( $padSeqStoreList ) )
    return;

  if ( ! isset ($padSeqStoreName) or ! $padSeqStoreName )
    $padSeqStoreName = $padSeqName;

  include '/pad/sequence/store/exits.php';

  $padSeqStore [$padSeqStoreName] = $padSeqResult;

?>