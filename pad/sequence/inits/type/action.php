<?php

  if ( isset ( $padSeqStore [ $padSeqTag ] ) ) {

    $padSeqPull = $padSeqTag;

    if ( ( $padSeqPrefix == 'action' ) ) { 
      $padSeqActionName  = $padSeqPrm1;
      $padSeqActionParms = $padSeqPrm2;
    } else {
      $padSeqActionName  = $padSeqPrefix;
      $padSeqActionParms = $padSeqPrm1;
    }

  } elseif ( isset ( $padSeqStore [ $padSeqPrefix ] ) ) {

    $padSeqPull = $padSeqPrefix ;

    $padSeqActionName  = $padSeqTag;
    $padSeqActionParms = $padSeqPrm1;

  } elseif ( isset ( $padSeqStore [ $padSeqPrm1 ] ) ) {

    $padSeqPull = $padSeqPrm1 ;

    if ( ( $padSeqPrefix == 'action' ) ) {
      $padSeqActionName  = $padSeqTag;
      $padSeqActionParms = $padSeqPrm2;
    } else {
      $padSeqActionName  = $padSeqTag;
      $padSeqActionParms = $padSeqPrm2;
    }

  }

  include 'sequence/inits/go/action.php';
 
?>