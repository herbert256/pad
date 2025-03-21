<?php

  // {make:sequence, 'add|5'}

  $padSeqPull = $padTag [$pad];

  $padSeqPlayParms     = padExplode ( $padParm, '|' );
  $padSeqPlayOperation = $padSeqPlayParms [0] ?? '';
  $padSeqPlayParm      = $padSeqPlayParms [1] ?? '';

  $padSeqPlayAction = $padType [$pad];
 
  include 'sequence/inits/go/play.php';


?>