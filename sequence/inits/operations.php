<?php

  $padSeqOprList = [];
  $padSeqOprType = 'make';

  foreach ( $padPrmParse as $padV ) {

    list ( $padSeqOprName, $padSeqOprValue ) = padSplit ( '=', trim($padV) );

    if ( in_array ( $padSeqOprName, ['make','keep','remove'] ) ) {
      $padSeqOprType = $padSeqOprName;
      continue;
    }

    if ( ! $padSeqOprName or $padSeqOprName == 'random' or $padSeqOprName == $padSeqSeq or ! file_exists ( "/pad/sequence/types/$padSeqOprName" ) )
      continue;

    $padSeqOprBld = '';

    if ( $padSeqOprType == 'keep' or $padSeqOprType == 'remove' ) 
      if ( file_exists ( "/pad/sequence/types/$padSeqOprName/bool.php" ) )
        $padSeqOprBld = 'bool';
    
    if ( $padSeqOprType == 'make' ) {
      if ( file_exists ( "/pad/sequence/types/$padSeqOprName/make.php" ) )
        $padSeqOprBld = 'make';
      elseif ( file_exists ( "/pad/sequence/types/$padSeqOprName/loop.php" ) )
        $padSeqOprBld = 'loop';
    }

    if ( ! $padSeqOprBld )
      $padSeqOprBld = padSeqMakeType ( "/pad/sequence/types/$padSeqOprName" );

    if ( $padSeqOprBld == 'bool' or $padSeqOprBld == 'function' ) 
      include_once "/pad/sequence/types/$padSeqOprName/$padSeqOprBld.php";
   
    $padSeqOprList [] = [
      'padSeqOprName'  => $padSeqOprName,
      'padSeqOprValue' => padEval ( $padSeqOprValue ),
      'padSeqOprBld'   => $padSeqOprBld,
      'padSeqOprType'  => $padSeqOprType
    ];

  }

?>