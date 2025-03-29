<?php

  // sequence:add mySequence, make, 3   } {$mySequence} {/sequence:add}

  if ( ! file_exists ( "sequence/types/$padSeqTag" ) )
    padError ('oeps');

  $padPrmValue    = $padSeqParm;
  $padSeqSeq      = $padSeqTag;
  $padSeqPlaySave = '';

  if ( in_array ( $padSeqFirst, ['make','keep','remove'] ) and isset ( $padSeqStore [$padSeqSecond] ) ) {

    $padSeqPull    = $padSeqSecond;
    $padSeqPlay    = $padSeqFirst;
    $padSeqDone [] = $padSeqFirst;
    $padSeqDone [] = $padSeqSecond;
  
  } elseif ( in_array ( $padSeqSecond, ['make','keep','remove'] ) and isset ( $padSeqStore [$padSeqFirst] ) ) {
  
    $padSeqPull    = $padSeqFirst;
    $padSeqPlay    = $padSeqSecond;      
    $padSeqDone [] = $padSeqFirst;
    $padSeqDone [] = $padSeqSecond;
  
  } else {

    if ( isset ( $padSeqStore [$padSeqSecond] ) ) {
      $padSeqPull    = $padSeqSecond;
      $padSeqPlay    = 'make';
      $padSeqDone [] = $padSeqSecond;
    } elseif ( isset ( $padSeqStore [$padSeqFirst] ) ) {
      $padSeqPull    = $padSeqFirst;
      $padSeqPlay    = 'make';
      $padSeqDone [] = $padSeqFirst;
    } 

    if ( in_array ( $padSeqFirst, ['make','keep','remove'] ) ) {
      $padSeqPlay    = $padSeqFirst;
      $padSeqDone [] = $padSeqFirst;
    } elseif ( in_array ( $padSeqSecond, ['make','keep','remove'] ) ) {
      $padSeqPlay    = $padSeqSecond;
      $padSeqDone [] = $padSeqSecond;
    }
  
  }

  if ( $padSeqPull ) {

      $padPrmValue    = $padSeqParm;
      $padSeqPlaySave = '';

      include 'sequence/plays/add.php';

      $padSeqLast = FALSE;

      include 'sequence/inits/go/store.php';

  } else {

    include 'sequence/inits/go/sequence.php';

  }

?>