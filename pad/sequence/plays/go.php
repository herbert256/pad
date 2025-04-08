<?php

  $padSeqPlayType = 'build';

  foreach ( $padSeqPlays as $padSeqPlay ) {

    extract ( $padSeqPlay );

    $padSeqLoop = $padSeq;

    include 'sequence/plays/one.php';

    if ( $padSeq === FALSE )
      return;

  }

?>