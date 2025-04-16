<?php

  $padSeqSave1 = $padSeqSeq;
  $padSeqSave2 = $padSeqBuild;
  
  $padSeqSeq   = $padPrmName;
  $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqPlay );

  include 'sequence/build/include.php';
  
  if ( file_exists ( "sequence/types/$padSeqSeq/init.php" ) )
    include "sequence/plays/init.php";
  
  $padSeqPlays [] = [
    'padSeqSeq'   => $padSeqSeq,
    'padSeqParm'  => $padPrmValue,
    'padSeqBuild' => $padSeqBuild,
    'padSeqPlay'  => $padSeqPlay
  ];

  $padSeqDone [] = $padSeqSeq;

  $padSeqSeq   = $padSeqSave1;
  $padSeqBuild = $padSeqSave2;

?>