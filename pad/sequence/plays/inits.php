<?php

  $padSeqSeqSave    = $padSeqSeq;
  $padSeqType       = 'make';
  $padSeqTypeSave   = '';

  foreach ( $padSeqOptions as $padSeqOptionKey => $padSeqOption ) {

    extract ( $padSeqOption );
    
    if ( $padPrmName == $padSeqSeqSave )
      continue;

    $padSeqSeq = $padPrmName;

    if ( in_array ( $padSeqSeq, ['make','keep','remove'] ) ) {
      $padSeqType = $padSeqSeq;
      continue;
    }

    include 'sequence/plays/add.php';

  }

  $padSeqSeq = $padSeqSeqSave;

?>