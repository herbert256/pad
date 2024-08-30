<?php

  if ( $padPair [$pad] and ! count ( $padSeqStoreList ) )
    return;

  if ( count ( $padSeqStoreList ) )
    $padSeqReturn = $padSeqResult;

  include '/pad/sequence/store/exits.php';

?>