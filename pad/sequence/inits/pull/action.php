<?php

  $padSeqActionName = '';

  if ( file_exists ( "sequence/actions/types/$padSeqTag.php" ) )

    $padSeqActionName = $padSeqTag;

  elseif ( file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) )

    $padSeqActionName = $padSeqPrefix;

  elseif ( file_exists ( "sequence/actions/types/$padSeqFirst.php" ) ) {

    $padSeqActionName = $padSeqFirst ;
    $padSeqParm       = $padSeqFirstParm;
    $padSeqDone []    = $padSeqFirst;

  } elseif ( file_exists ( "sequence/actions/types/$padSeqSecond.php" ) ) {

    $padSeqActionName = $padSeqSecond ;
    $padSeqParm       = $padSeqSecondParm;
    $padSeqDone []    = $padSeqSecond;

  }

  if ( $padSeqActionName ) {
    $padSeqPull       = $padSeqPullName;
    $padSeqPullName   = '';
  }

?>