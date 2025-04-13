<?php

  if ( $padSeqSeq )
    return;

  foreach ( $padSeqPlays as $padSeqPlay )
    if ( padSeqPlay ( $padSeqPlay ['padSeqPlay'] ) )
      return;

  padSeqSequence ( $padSeqSeq, $padSeqParm );
  
?>