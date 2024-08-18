<?php

  $padSeqOprList = [];
  $padSeqOprType = 'make';

  foreach ( $padPrmParse as $padV ) {

    list ( $padPrmName, $padPrmValue ) = padSplit ( '=', trim($padV) );

    if ( in_array ( $padPrmName, ['make','keep','remove'] ) ) {
      $padSeqOprType = $padPrmName;
      continue;
    }

    if ( $padPrmName <> $padSeqSeq and file_exists ( "/pad/sequence/types/$padPrmName" ) )
      $padSeqOprList [] = [
        'padSeqOprName'  => $padPrmName,
        'padSeqOprValue' => padEval ( $padPrmValue ),
        'padSeqOprBld'   => padSeqMakeType ( "/pad/sequence/types/$padPrmName" ),
        'padSeqOprType'  => $padSeqOprType
      ];

  }

?>