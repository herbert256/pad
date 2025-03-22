<?php

  // {make:sequence, 'add|5'}
  // {make:add, 'sequence|5'}

  $padSeqPlayAction = $padType [$pad];
  $padSeqPlayParms  = padExplode ( $padParm, '|' );
  $padSeqPlayParm   = $padSeqPlayParms [1] ?? '';

  $padTmpFirst  = $padTag [$pad]; 
  $padTmpSecond = $padSeqPlayParms [0] ?? ''; 

  if ( isset ( $padSeqStore[$padTmpFirst] ) and file_exists ( "sequence/types/$padTmpSecond" ) ) {

    $padSeqPull          = $padTmpFirst;
    $padSeqPlayOperation = $padTmpSecond;
  
  } elseif ( isset ( $padSeqStore[$padTmpSecond] ) and file_exists ( "sequence/types/$padTmpFirst" ) ) {

    $padSeqPull          = $padTmpSecond;
    $padSeqPlayOperation = $padTmpFirst;

  } else {

   $padSeqInfo ['errors'] [] = "$padPage-no_start-play-$padTmpFirst-$padTmpSecond";
   return;
  
  }

  include 'sequence/inits/go/play.php';

?>