<?php

  $padSeqPull = '';
  $padSeqSeq  = '';
  $padSeqPlay = '';

  include "sequence/inits/$padSeqInit/play.php";

  if ( ! $padSeqPull and $padSeqPullName )
    include "sequence/inits/pull/play.php";

  if ( $padSeqPull )
    return include 'sequence/inits/go/play.php';

?>