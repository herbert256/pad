<?php

  if ( isset ( $padSeqStore [ $padSeqTag ] ) ) {

    // {demo} {action:mySequence 'last', 3 } 
    // {demo} {last:mySequence 3           } 

    $padSeqPull = $padSeqTag;

    if ( ( $padSeqPrefix == 'action' ) ) { 
      padSeqCorrectParm2 ();
      $padSeqActionName  = $padPrm1;
      $padSeqActionParms = $padPrm2;
    } else {
      $padSeqActionName  = $padSeqPrefix;
      $padSeqActionParms = $padPrm1;
    }

  } elseif ( isset ( $padSeqStore [ $padSeqPrefix ] ) ) {

    // {demo} {mySequence:last 3 }

    $padSeqPull = $padSeqPrefix ;

    $padSeqActionName  = $padSeqTag;
    $padSeqActionParms = $padPrm1;

  } elseif ( isset ( $padSeqStore [ $padPrm1 ] ) ) {

    // {demo} {action:last 'mySequence', 3    } 
    // {demo} {last 'mySequence', 3    } 

    padSeqCorrectParm2 ();

    $padSeqPull = $padPrm1 ;

    if ( ( $padSeqPrefix == 'action' ) ) {
      $padSeqActionName  = $padSeqTag;
      $padSeqActionParms = $padPrm2;
    } else {
      $padSeqActionName  = $padSeqTag;
      $padSeqActionParms = $padPrm2;
    }

  }

  include 'sequence/inits/go/action.php';
 
?>