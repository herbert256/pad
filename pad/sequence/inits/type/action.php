<?php

  if ( $padSeqPrefix )
    if  ( ( $padSeqPrefix == 'action' and file_exists ( "sequence/types/$padSeqTag"    ) )
      or  ( $padSeqTag    == 'action' and file_exists ( "sequence/types/$padSeqPrefix" ) ) ) {

    padSeqPull   ( $padSeqPull );
    padSeqAction ( $padSeqAction, $padSeqParm );

    $padSeqSeq = ( $padSeqPrefix == 'action') ? $padSeqTag : $padSeqPrefix;

    include 'sequence/inits/type/pull/sequence.php';

  }

  if ( isset ( $padSeqStore [ $padSeqTag ] ) and file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) ) {

    $padSeqPull   = $padSeqTag;
    $padSeqAction = $padSeqPrefix;

  } elseif ( isset ( $padSeqStore [ $padSeqPrefix ] ) and file_exists ( "sequence/actions/types/$padSeqTag.php" ) ) {

    $padSeqPull   = $padSeqPrefix;
    $padSeqAction = $padSeqTag;

  } elseif ( isset ( $padSeqStore [ $padSeqTag ] ) and file_exists ( "sequence/actions/types/$padSeqFirst.php" ) ) {

    $padSeqPull    = $padSeqTag;
    $padSeqAction  = $padSeqFirst;
    $padSeqDone [] = $padSeqFirst;

    if ( $padSeqFirstParm and $padSeqFirstParm !== TRUE and ! $padSeqParm )
      $padSeqParm = $padSeqFirstParm;

  } elseif ( isset ( $padSeqStore [ $padSeqPrefix ] ) and file_exists ( "sequence/actions/types/$padSeqFirst.php" ) ) {

    $padSeqPull    = $padSeqPrefix;
    $padSeqAction  = $padSeqFirst ;
    $padSeqDone [] = $padSeqFirst;

    if ( $padSeqFirstParm and $padSeqFirstParm !== TRUE and ! $padSeqParm )
      $padSeqParm = $padSeqFirstParm;

  } elseif ( isset ( $padSeqStore [ $padSeqTag ] ) and file_exists ( "sequence/actions/types/$padSeqSecond.php" ) ) {

    $padSeqPull    = $padSeqTag;
    $padSeqAction  = $padSeqSecond;
    $padSeqDone [] = $padSeqSecond;

    if ( $padSeqSecondParm and $padSeqSecondParm !== TRUE and ! $padSeqParm )
      $padSeqParm = $padSeqSecondParm;

  } elseif ( isset ( $padSeqStore [ $padSeqPrefix ] ) and file_exists ( "sequence/actions/types/$padSeqSecond.php" ) ) {

    $padSeqPull    = $padSeqPrefix;
    $padSeqAction  = $padSeqSecond ;
    $padSeqDone [] = $padSeqSecond;

    if ( $padSeqSecondParm and $padSeqSecondParm !== TRUE and ! $padSeqParm )
       $padSeqParm = $padSeqSecondParm;

  } elseif ( isset ( $padSeqStore [ $padSeqFirst ] ) and file_exists ( "sequence/actions/types/$padSeqTag.php" ) ) {

    $padSeqPull    = $padSeqFirst;
    $padSeqAction  = $padSeqTag;
    $padSeqDone [] = $padSeqFirst;

  } elseif ( isset ( $padSeqStore [ $padSeqFirst ] ) and file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) ) {

    $padSeqPull    = $padSeqFirst;
    $padSeqAction  = $padSeqPrefix ;
    $padSeqDone [] = $padSeqFirst;

  } 

?>