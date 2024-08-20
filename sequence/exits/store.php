<?php

  if ( $padSeqPull and ( isset ($padSeqPop) or isset ($padSeqShift) ) ) {

    if ( $padSeqPull <> $padSeqName )
      $padSeqStore [$padSeqName] = $padSeqStore [$padSeqPull];

    return;

  }

  if ( $padPair [$pad] )
    return;

  $padSeqStore [$padSeqName] = $padSeqResult;

?>