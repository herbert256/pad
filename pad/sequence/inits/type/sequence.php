<?php

  include 'sequence/inits/type/sequence/check.php';
 
  if ( ! $padSeqPrefix and isset ( $padSeqStore [$padSeqTag] ) ) {
    $padSeqPull = $padSeqTag;
    return include 'sequence/inits/go/store.php';
  }

  if ( isset ( $padSeqStore [$padSeqPrefix] ) and $padSeqTag == 'sequence' ) {
    $padSeqPull = $padSeqPrefix;
    return include 'sequence/inits/go/store.php';
  }

  if ( isset ( $padSeqStore [$padSeqTag] ) and $padSeqPrefix == 'sequence' ) {
    $padSeqPull = $padSeqTag;
    return include 'sequence/inits/go/store.php';
  }

  $padPrmValue = $padSeqParm;

  if ( file_exists ( "sequence/types/$padSeqTag" ) ) 
    $padSeqSeq = $padSeqTag;
  elseif ( $padSeqPrefix and file_exists ( "sequence/types/$padSeqPrefix" ) ) 
    $padSeqSeq = $padSeqPrefix;

  if ( $padSeqPullName ) {

    $padSeqPull     = $padSeqPullName;
    $padSeqPullName = '';
    $padSeqPlay     = 'make';
  
  } elseif ( padSeqPlay ( $padSeqFirst ) and isset ( $padSeqStore [$padSeqSecond] ) ) {

    $padSeqPull    = $padSeqSecond;
    $padSeqPlay    = $padSeqFirst;
    $padSeqDone [] = $padSeqFirst;
    $padSeqDone [] = $padSeqSecond;
  
  } elseif ( padSeqPlay ( $padSeqSecond ) and isset ( $padSeqStore [$padSeqFirst] ) ) {
  
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

  }

  if ( $padSeqPull and $padSeqSeq and $padSeqPlay ) 
    return include 'sequence/plays/extra.php';

  if ( $padSeqPull ) 
    return include 'sequence/inits/go/store.php';
  
?>