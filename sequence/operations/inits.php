<?php

  $padSeqSeqSave   = $padSeqSeq;
  $padSeqBuildSave = $padSeqBuild;

  $padSeqOperations = [];
  $padSeqType = 'make';

  foreach ( $padPrmParse as $padV ) {

    list ( $padSeqSeq, $padSeqValue ) = padSplit ( '=', trim($padV) );

    if ( in_array ( $padSeqSeq, ['make','keep','remove'] ) ) {
      $padSeqType = $padSeqSeq;
      continue;
    }

    if ( ! $padSeqSeq or $padSeqSeq == 'random' or $padSeqSeq == $padSeqSeqSave or $padSeqSeq == $padSeqPull )
      continue;

    if ( ! file_exists ( "/pad/sequence/types/$padSeqSeq" ) )
      continue;

    $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqType );

    include "/pad/sequence/build/include.php";

    $padSeqOperations [] = [
      'padSeqSeq'   => $padSeqSeq,
      'padSeqName'  => $padSeqSeq,
      'padSeqParm'  => padEval ( $padSeqValue ),
      'padSeqBuild' => $padSeqBuild,
      'padSeqType'  => $padSeqType
    ];

  }

  $padSeqSeq   = $padSeqSeqSave;
  $padSeqBuild = $padSeqBuildSave;

?>