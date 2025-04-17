<?php

  $pqSave1 = $pqSeq;
  $pqSave2 = $pqBuild;
  
  $pqSeq   = $padPrmName;
  $pqBuild = pqBuild ( $pqSeq, $pqPlay );

  include 'sequence/build/include.php';
  
  if ( file_exists ( "sequence/types/$pqSeq/init.php" ) )
    include "sequence/plays/init.php";
  
  $pqPlays [] = [
    'pqSeq'   => $pqSeq,
    'pqParm'  => $padPrmValue,
    'pqBuild' => $pqBuild,
    'pqPlay'  => $pqPlay
  ];

  $pqDone [] = $pqSeq;

  $pqSeq   = $pqSave1;
  $pqBuild = $pqSave2;

?>