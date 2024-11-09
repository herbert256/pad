<?php

  $padSeqSeqSave    = $padSeqSeq;
  $padSeqType       = 'make';
  $padSeqTypeSave   = '';

  foreach ( $padSeqOptions as  $padSeqOption ) {

    extract (  $padSeqOption );
    
    if ( $padPrmName == $padSeqSeqSave )
      continue;

    $padSeqSeq = $padPrmName;

    if ( in_array ( $padSeqSeq, ['make','keep','remove'] ) ) {
      $padSeqType = $padSeqSeq;
      continue;
    }

    include '/pad/sequence/operations/add.php';

  }

  $padSeqSeq = $padSeqSeqSave;

?>