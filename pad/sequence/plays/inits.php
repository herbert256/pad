<?php

  $padSeqType     = 'make';
  $padSeqTypeSave = '';

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( in_array ( $padPrmName, $padSeqDone ) )
      continue;
    
    if ( $padPrmName == $padSeqSeqSave )
      continue;

    $padSeqSeq = $padPrmName;

    if ( in_array ( $padSeqSeq, ['make','keep','remove'] ) ) {
      $padSeqType = $padSeqSeq;
      continue;
    }

    include 'sequence/plays/add.php';

  }

?>