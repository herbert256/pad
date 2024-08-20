<?php

  $padSeqList = [];
  $padSeqType = 'make';

  foreach ( $padPrmParse as $padV ) {

    list ( $padSeqOpr, $padSeqValue ) = padSplit ( '=', trim($padV) );

    if ( in_array ( $padSeqOpr, ['make','keep','remove'] ) ) {
      $padSeqType = $padSeqOpr;
      continue;
    }

    if ( ! $padSeqOpr or $padSeqOpr == 'random' or $padSeqOpr == $padSeqSeq )
      continue;

    if ( ! file_exists ( "/pad/sequence/types/$padSeqOpr" ) )
      continue;

    $padSeqOprBld = padSeqBuild ( $padSeqOpr, $padSeqType );

    include "/pad/sequence/build/include.php";

    $padSeqList [] = [
      'padSeqSeq'   => $padSeqOpr,
      'padSeqName'  => $padSeqOpr,
      'padSeqParm'  => padEval ( $padSeqValue ),
      'padSeqBuild' => $padSeqOprBld,
      'padSeqType'  => $padSeqType
    ];

  }

?>