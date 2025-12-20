<?php

  $pqSave1 = $pqSeq;
  $pqSave2 = $pqBuild;

  $pqSeq   = $padPrmName;
  $pqBuild = pqBuild ( $pqSeq, $pqPlay );

  include PQ . 'build/include.php';
  include PQ . "plays/init.php";

  $pqPlays [] = [
    'pqSeq'   => $pqSeq,
    'pqParm'  => $padPrmValue,
    'pqBuild' => $pqBuild,
    'pqPlay'  => $pqPlay
  ];

  if ( ( $padK = array_search ( $pqSeq, $padDone ) ) !== false )
    unset ( $padDone [$padK] );

  $pqSeq   = $pqSave1;
  $pqBuild = $pqSave2;

?>