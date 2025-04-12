<?php

  $padSeqPull = '';
  $padSeqSeq  = '';
  $padSeqPlay = '';

  include "sequence/inits/$padSeqInitType/play.php";

  if ( ! $padSeqPull and $padSeqPullName )
    include "sequence/inits/pull/play.php";

  if ( $padSeqPull )
    return include 'sequence/inits/go/play.php';

  include 'sequence/inits/play/type.php';

  if ( $padSeqSeq )
    return;

  include 'sequence/inits/play/check.php';

  $padSeqSeq   = '';
  $padSeqBuild = '';

  include 'sequence/inits/tag/sequence.php'; 

?>