<?php

  $padSeqAction = '';

  if ( file_exists ( "sequence/actions/types/$padSeqTag.php" ) )

    $padSeqAction = $padSeqTag;

  elseif ( file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) )

    $padSeqAction = $padSeqPrefix;

  elseif ( file_exists ( "sequence/actions/types/$padSeqFirst.php" ) ) {

    $padSeqAction = $padSeqFirst ;
    $padSeqParm       = $padSeqFirstParm;
    $padSeqDone []    = $padSeqFirst;

  } elseif ( file_exists ( "sequence/actions/types/$padSeqSecond.php" ) ) {

    $padSeqAction = $padSeqSecond ;
    $padSeqParm       = $padSeqSecondParm;
    $padSeqDone []    = $padSeqSecond;

  }

  if ( $padSeqAction ) {
    $padSeqPull       = $padSeqPullName;
    $padSeqPullName   = '';
  }

?>