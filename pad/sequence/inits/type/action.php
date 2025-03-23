<?php

  if ( isset ( $padSeqStore [ $padTag [$pad] ] ) ) {

    // {demo} {action:mySequence 'last', 3 } 
    // {demo} {last:mySequence 3           } 

    $padSeqPull = $padTag [$pad];

    if ( ( $padPrefix[$pad] == 'action' ) ) { 
      $padSeqActionName  = $padOpt [$pad] [1];
      $padSeqActionParms = $padOpt [$pad] [2];
    } else {
      $padSeqActionName  = $padPrefix[$pad];
      $padSeqActionParms = $padOpt [$pad] [1];
    }

  } elseif ( isset ( $padSeqStore [ $padPrefix [$pad] ] ) ) {

    // {demo} {mySequence:last 3 }

    $padSeqPull = $padPrefix [$pad] ;

    $padSeqActionName  = $padTag [$pad];
    $padSeqActionParms = $padOpt [$pad] [1];

  } elseif ( isset ( $padSeqStore [ $padOpt [$pad] [1] ] ) ) {

    // {demo} {action:last 'mySequence', 3    } 
    // {demo} {last 'mySequence', 3    } 

    $padSeqPull = $padOpt [$pad] [1] ;

    if ( ( $padPrefix[$pad] == 'action' ) ) {
      $padSeqActionName  = $padTag [$pad];
      $padSeqActionParms = $padOpt [$pad] [2];
    } else {
      $padSeqActionName  = $padTag [$pad];
      $padSeqActionParms = $padOpt [$pad] [2];
    }

  }

  include 'sequence/inits/go/action.php';
 
?>