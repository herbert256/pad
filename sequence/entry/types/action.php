<?php

  $padSeqActionName = $padTag [$pad];
  $padSeqActionParm = '';

  if ( file_exists ( "/pad/sequence/actions/double/$padSeqActionName") ) {

    $padSeqActionParm = $padFirstParm [$pad];

    $padSeqNoNo [$padSeqActionParm] = TRUE;
    
    if ( $padSeqActionParm == $padOpt [$pad] [1] ?? '' ) 
      $padSeqEntryParm = $padOpt [$pad] [2] ?? '';
    else
      unset ( $padSeqEntryList [$padSeqActionParm] );

    $padSeqTmp = padExplode ( $padSeqActionParm, '|' );

    if ( count ( $padSeqTmp ) > 1 ) {
      $padSeqSetStore   = array_shift ( $padSeqTmp );
      $padSeqActionParm = implode ( '|', $padSeqTmp );
    }

  }

  $padSeqOptions [] = [ 
    'padPrmName'  => $padSeqActionName,
    'padPrmValue' => $padSeqActionParm 
  ];

  return include '/pad/sequence/sequence.php';

?>