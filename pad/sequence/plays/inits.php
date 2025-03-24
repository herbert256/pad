<?php

  $padSeqPlay     = 'make';
  $padSeqPlaySave = '';

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( in_array ( $padPrmName, $padSeqDone ) )
      continue;
    
    if ( $padPrmName == $padSeqSeqSave )
      continue;

    if ( in_array ( $padPrmName, ['make','keep','remove'] ) and $padPrmValue and $padPrmValue !== TRUE ) {
      $padSeqPlay  = $padPrmName;
      $padExplode  = explode ('|', $padPrmValue, 2); 
      $padSeqSeq   = $padExplode [0] ?? '';
      $padPrmValue = $padExplode [1] ?? '';   
      include 'sequence/plays/add.php';     
      continue;
    }

    if ( in_array ( $padPrmName, ['make','keep','remove'] ) ) {
      $padSeqPlay = $padPrmName;
      continue;
    }

    $padSeqSeq = $padPrmName;

    include 'sequence/plays/add.php';

  }

?>