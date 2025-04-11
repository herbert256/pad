<?php

  if ( ! $padSeqPullName ) 
    return;

  if ( $padSeqPullName == $padSeqPull ) {
    $padSeqPullName = '';
    return;
  }

  if ( $padSeqPull )
    padError ('Double store names found');

  $padSeqPull = $padSeqPullName;

  if ( $padSeqSeq and file_exists ( "sequence/types/$padSeqSeq" ) ) {
  
    $padPrmValue = $padSeqParm;

    if     ( padSeqPlay ( $padSeqType ) ) $padSeqPlay = $padSeqType;
    elseif ( padSeqPlay ( $padSeqTag  ) ) $padSeqPlay = $padSeqTag;
    else                                  $padSeqPlay = 'make';
  
    return include 'sequence/plays/extra.php';
  
  }

  include 'sequence/inits/go/store.php';

?>