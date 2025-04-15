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

  if ( ! $padSeqPull ) 
    padSeqPull ( $padSeqPull );

  if ( $padSeqPull )
    return include 'sequence/inits/go/store.php';

  $padSeqBuild = '';

  include 'sequence/inits/seqseq.php'; 

?>