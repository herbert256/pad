<?php

  if ( isset ( $padSeqStore [ $padSeqTag ] ) ) {

    // {demo} {action:mySequence 'last', 3 } 
    // {demo} {last:mySequence 3           } 

    $padSeqPull = $padSeqTag;

    if ( ( $padSeqPrefix == 'action' ) ) { 
      padSeqCorrectParm2 ();
      $padSeqActionName  = $padSeqParm1;
      $padSeqActionParms = $padSeqParm2;
    } else {
      $padSeqActionName  = $padSeqPrefix;
      $padSeqActionParms = $padSeqParm1;
    }

  } elseif ( isset ( $padSeqStore [ $padSeqPrefix ] ) ) {

    // {demo} {mySequence:last 3 }

    $padSeqPull = $padSeqPrefix ;

    $padSeqActionName  = $padSeqTag;
    $padSeqActionParms = $padSeqParm1;

  } elseif ( isset ( $padSeqStore [ $padSeqParm1 ] ) ) {

    // {demo} {action:last 'mySequence', 3    } 
    // {demo} {last 'mySequence', 3    } 

    padSeqCorrectParm2 ();

    $padSeqPull = $padSeqParm1 ;

    if ( ( $padSeqPrefix == 'action' ) ) {
      $padSeqActionName  = $padSeqTag;
      $padSeqActionParms = $padSeqParm2;
    } else {
      $padSeqActionName  = $padSeqTag;
      $padSeqActionParms = $padSeqParm2;
    }

  }

  include 'sequence/inits/go/action.php';
 
?>