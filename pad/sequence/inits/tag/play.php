<?php

  // {make 'sequence', 'add|5'}

  $padSeqPull = $padOpt [$pad] [1];

  $padSeqPlayParms     = padExplode ( $padOpt [$pad] [2], '|' );
  $padSeqPlayOperation = $padSeqPlayParms [0] ?? '';
  $padSeqPlayParm      = $padSeqPlayParms [1] ?? '';

  $padSeqPlayAction = $padTag [$pad];
 
  include 'sequence/inits/go/play.php';

?>