<?php

//{sequence:even mySequence, keep} {$mySequence} {/sequence:even}

  if ( isset ( $padSeqStore [$padSeqFirst] ) ) {

    $padSeqPull    = $padSeqFirst;
    $padSeqDone [] = $padSeqFirst;

    return include 'sequence/inits/go/store.php';

  }

  $padSeqSeq = $padSeqTag;

  include 'sequence/inits/go/sequence.php';

?>