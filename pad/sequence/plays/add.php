<?php
  
  if ( $padSeqSeq == 'start' ) kkk();

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

  $padSeqPlayKey = array_key_last ( $padSeqPlays );
  
  $padSeqDone [] = $padSeqSeq;

?>