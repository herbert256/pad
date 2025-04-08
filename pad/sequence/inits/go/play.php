<?php 

  $padSeqBuild = padSeqBuild ( $padSeqSeq, $padSeqPlay );

  include 'sequence/build/include.php';

  $padSeqFixed = [];

  $padSeqInfo ["start/$padSeqInit/play/$padSeqPlay"] [] = $padSeqSeq;

  $padSeqPlayType = 'init';

  foreach ( $padSeqStore [$padSeqPull] as $padSeqLoop ) {

    include 'sequence/plays/one.php';

    if ( $padSeq !== FALSE )
      $padSeqFixed [] = $padSeq;

  }

  include "sequence/inits/go/start.php";

  return TRUE;

?>