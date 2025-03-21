<?php 

  $padSeqSeq   = $padSeqPlayOperation;
  $padSeqParm  = $padSeqPlayParm;
  $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqPlayAction );

  include 'sequence/build/include.php';

  $padSeqFixed = [];

  foreach ( $padSeqStore [$padSeqPull] as $padSeqPlayKey => $padSeqLoop ) {

    $padSeq = include 'sequence/build/call.php';
  
    if     ( $padSeqPlayAction == 'make'   and $padSeq === TRUE       ) continue;
    elseif ( $padSeqPlayAction == 'make'   and $padSeq === FALSE      ) continue;
    elseif ( $padSeqPlayAction == 'make'   and $padSeq == $padSeqLoop ) $padSeqFixed [] = $padSeq;
    elseif ( $padSeqPlayAction == 'make'   and $padSeq <> $padSeqLoop ) $padSeqFixed [] = $padSeq;

    elseif ( $padSeqPlayAction == 'keep'   and $padSeq === TRUE       ) $padSeqFixed [] = $padSeqLoop;
    elseif ( $padSeqPlayAction == 'keep'   and $padSeq === FALSE      ) continue;
    elseif ( $padSeqPlayAction == 'keep'   and $padSeq == $padSeqLoop ) $padSeqFixed [] = $padSeq;
    elseif ( $padSeqPlayAction == 'keep'   and $padSeq <> $padSeqLoop ) continue;

    elseif ( $padSeqPlayAction == 'remove' and $padSeq === TRUE       ) continue;
    elseif ( $padSeqPlayAction == 'remove' and $padSeq === FALSE      ) $padSeqFixed [] = $padSeqLoop;
    elseif ( $padSeqPlayAction == 'remove' and $padSeq == $padSeqLoop ) continue;
    elseif ( $padSeqPlayAction == 'remove' and $padSeq <> $padSeqLoop ) $padSeqFixed [] = $padSeq;

  }

  include "sequence/inits/go/start.php";

?>