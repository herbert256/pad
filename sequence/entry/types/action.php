<?php

  $padSeqEntryName = $padTag [$pad];

  if ( file_exists ( "/pad/sequence/actions/single/$padSeqEntryName") ) {

    $padSeqOptions [] = [ 
      'padPrmName'  => $padSeqEntryName,
      'padPrmValue' => ''
    ];

  } else {

    $padSeqActionParm = $padFirstParm [$pad];

    $padSeqNoNo [$padSeqActionParm] = TRUE;
    
    if ( $padSeqActionParm == $padOpt [$pad] [1] ) 
      $padSeqEntryParm = $padOpt [$pad] [2] ?? '';
    else
      unset ( $padSeqEntryList [$padSeqActionParm] );

    $padSeqTmp = padExplode ( $padSeqActionParm );

    if ( count ( $padSeqTmp ) > 1 ) {
      $padSeqSetStore   = array_shift ( $padSeqTmp );
      $padSeqActionParm = implode ( '|', $padSeqTmp );
    }

    $padSeqOptions [] = [ 
      'padPrmName'  => $padSeqEntryName,
      'padPrmValue' => $padSeqActionParm 
    ];

  }

  return include '/pad/sequence/sequence.php';

?>