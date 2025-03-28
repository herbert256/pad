<?php

  if ( isset ( $padSeqStore [ $padSeqTag ] ) and file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) ) {

    $padSeqPull       = $padSeqTag;
    $padSeqActionName = $padSeqPrefix;

  } elseif ( isset ( $padSeqStore [ $padSeqPrefix ] ) and file_exists ( "sequence/actions/types/$padSeqTag.php" ) ) {

    $padSeqPull       = $padSeqPrefix;
    $padSeqActionName = $padSeqTag;

  } elseif ( isset ( $padSeqStore [ $padSeqTag ] ) and file_exists ( "sequence/actions/types/$padSeqFirst.php" ) ) {

    $padSeqPull       = $padSeqTag;
    $padSeqActionName = $padSeqFirst;
    $padSeqDone []    = $padSeqFirst;

    if ( $padSeqFirstParm and $padSeqFirstParm !== TRUE and ! $padSeqParm )
      $padSeqParm = $padSeqFirstParm;

  } elseif ( isset ( $padSeqStore [ $padSeqPrefix ] ) and file_exists ( "sequence/actions/types/$padSeqFirst.php" ) ) {

    $padSeqPull       = $padSeqPrefix;
    $padSeqActionName = $padSeqFirst ;
    $padSeqDone []    = $padSeqFirst;

    if ( $padSeqFirstParm and $padSeqFirstParm !== TRUE and ! $padSeqParm )
       $padSeqParm = $padSeqFirstParm;

  } elseif ( isset ( $padSeqStore [ $padSeqFirst ] ) and file_exists ( "sequence/actions/types/$padSeqTag.php" ) ) {

    $padSeqPull       = $padSeqFirst;
    $padSeqActionName = $padSeqTag;
    $padSeqDone []    = $padSeqFirst;

  } elseif ( isset ( $padSeqStore [ $padSeqFirst ] ) and file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) ) {

    $padSeqPull       = $padSeqFirst;
    $padSeqActionName = $padSeqPrefix ;
    $padSeqDone []    = $padSeqFirst;

  } 

?>