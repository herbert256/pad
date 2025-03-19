<?php

  include 'seq/inits/short/parms.php';

  $padSeqSeq   = $padTag [$pad];
  $padSeqBuild = padSeqBuild ( $padSeqSeq, 'loop' );
  $padSeqParm  = $padSeqStartParm;

  foreach ( $padSeqResult as $padSeqResultKey => $padSeqLoop ) {

    $padSeq = include 'seq/build/call.php';

    if     ( $padSeqType == 'keep'   and $padSeq === TRUE       ) continue;
    elseif ( $padSeqType == 'keep'   and $padSeq === FALSE      ) unset ( $padSeqResult [$padSeqResultKey] );
    elseif ( $padSeqType == 'keep'   and $padSeq <> $padSeqLoop ) unset ( $padSeqResult [$padSeqResultKey] );
    elseif ( $padSeqType == 'keep'                              ) continue;
    elseif ( $padSeqType == 'remove' and $padSeq === TRUE       ) unset ( $padSeqResult [$padSeqResultKey] );
    elseif ( $padSeqType == 'remove' and $padSeq === FALSE      ) continue;
    elseif ( $padSeqType == 'remove' and $padSeq == $padSeqLoop ) unset ( $padSeqResult [$padSeqResultKey] );
    elseif ( $padSeqType == 'remove'                            ) continue;
    elseif ( $padSeqType == 'make'   and $padSeq === FALSE      ) unset ( $padSeqResult [$padSeqResultKey] );
    elseif ( $padSeqType == 'make'   and $padSeq === TRUE       ) continue;
    elseif ( $padSeqType == 'make'   and $padSeq == $padSeqLoop ) continue;
    elseif ( $padSeqType == 'make'                              ) $padSeqResult [$padSeqResultKey] = $padSeq;

  }

  $padSeqStartArray = $padSeqResult;

  include 'seq/inits/short/get.php';

?>