<?php

  if ( $padSeqSeq == 'range'   ) return;
  if ( $padSeqRows             ) return;
  if ( $padSeqTo < PHP_INT_MAX ) return;

  if ( $padSeqRandom and $padSeqMax )
    $padSeqTo = $padSeqLoopEnd = $padSeqMax;

  $padSeqRows = $padSeqSave;

?>