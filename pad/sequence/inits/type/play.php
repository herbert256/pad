<?php

  // {make:sequence, 'add|5'}

  $padSeqPull = $padTag [$pad] [1];

  $padSeqPlayParms     = padExplode ( $padParm, '|' );
  $padSeqPlayOperation = $padSeqPlayParms [0] ?? '';
  $padSeqPlayParm      = $padSeqPlayParms [1] ?? '';

  $padPlayAction = $padType [$pad];
 
  include 'sequence/inits/play/play.php';


?>