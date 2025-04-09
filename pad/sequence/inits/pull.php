<?php

  if ( ! $padSeqPullName ) 
    return;

  if ( $padSeqPullName == $padSeqPull ) {
    $padSeqPullName = '';
    return;
  }

  if ( $padSeqPull )
    padError ('Double store names found');

  if ( $padSeqSeq and file_exists ( "sequence/types/$padSeqSeq" ) {
    $padPrmValue = $padSeqParm;
    $padSeqPlay  = 'make';
    include 'sequence/plays/add.php';
  }

  $padSeqPull  = $padSeqPullName;
  $padSeqBuild = 'pull';
  $padSeqSeq   = 'pull';
  $padSeqParm  = '';

?>