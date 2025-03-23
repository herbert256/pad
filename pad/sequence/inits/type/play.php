<?php

  // {make:sequence, 'add|5'}
  // {make:add, 'sequence|5'}

  $padSeqPlayAction = $padSeqTypeFirst;
  $padSeqPlayParms  = padExplode ( $padParm, '|' );
  $padSeqPlayParm   = $padSeqPlayParms [1] ?? '';

  $padTmpFirst  = $padSeqFirst; 
  $padTmpSecond = $padSeqPlayParms [0] ?? ''; 

  if ( isset ( $padSeqStore[$padTmpFirst] ) and file_exists ( "sequence/types/$padTmpSecond" ) ) {

    $padSeqPull          = $padTmpFirst;
    $padSeqPlayOperation = $padTmpSecond;
  
  } elseif ( isset ( $padSeqStore[$padTmpSecond] ) and file_exists ( "sequence/types/$padTmpFirst" ) ) {

    $padSeqPull          = $padTmpSecond;
    $padSeqPlayOperation = $padTmpFirst;

  } 

  include 'sequence/inits/go/play.php';

?>