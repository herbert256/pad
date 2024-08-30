<?php

  $padSeqSeqSave   = $padSeqSeq;
  $padSeqBuildSave = $padSeqBuild;

  $padSeqOperations = [];
  $padSeqType = 'make';

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    $padSeqSeq = $padPrmName;

    if ( in_array ( $padSeqSeq, ['make','keep','remove'] ) ) {
      $padSeqType = $padSeqSeq;
      continue;
    }

    if ( ! $padSeqSeq or $padSeqSeq == 'random' or $padSeqSeq == $padSeqSeqSave or $padSeqSeq == $padSeqPull 
      or ! file_exists ( "/pad/sequence/types/$padSeqSeq" ) )
      continue;

    $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqType );

    include '/pad/sequence/build/include.php';

    $padSeqOperations [] = [
      'padSeqSeq'   => $padSeqSeq,
      'padSeqParm'  => $padPrmValue,
      'padSeqBuild' => $padSeqBuild,
      'padSeqType'  => $padSeqType
    ];

  }

  $padSeqSeq   = $padSeqSeqSave;
  $padSeqBuild = $padSeqBuildSave;

?>