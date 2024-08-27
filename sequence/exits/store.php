<?php

  if ( $padPair [$pad] and ! count ( $padSeqStoreList ) )
    return;

  include '/pad/sequence/store/exits.php';

  $padSeqStore [$padSeqName] = $padSeqResult;

?>