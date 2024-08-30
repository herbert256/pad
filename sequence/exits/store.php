<?php

  if ( $padPair [$pad] and ! count ( $padSeqStoreList ) )
    return;

  if ( count ( $padSeqStoreList ) )
    include '/pad/sequence/store/exits.php';
  else
    include '/pad/sequence/exits/single.php';

?>