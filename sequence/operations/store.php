<?php

  $padStoreIndex = count ( $padSeqResult );

  if ( ! isset ( $padSeqStore [$padSeqParm] ) )
    padError ( "Sequence: Store not found: $padSeqSeq -> $padSeqParm");

  if ( ! isset ( $padSeqStore [$padSeqParm] [$padStoreIndex] ) )
    padError ( "Sequence: Store entry not found: $padSeqSeq/$padSeqParm/$padStoreIndex");

  $padSeqParm = $padSeqStore [$padSeqParm] [$padStoreIndex];

?>