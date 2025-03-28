<?php

  if ( $padSeqFirst and $padSeqSecond )

    if ( isset ( $padSeqStore [ $padSeqFirst ] ) and file_exists ( "sequence/actions/types/$padSeqSecond.php" ) ) {

      $padSeqPull       = $padSeqFirst;   
      $padSeqActionName = $padSeqSecond;

      if ( $padSeqSecondParm and $padSeqSecondParm !== TRUE and ! $padSeqParm )
        $padSeqParm = $padSeqSecondParm;

    } elseif ( isset ( $padSeqStore [ $padSeqSecond ] ) and file_exists ( "sequence/actions/types/$padSeqFirst.php" ) ) {

      $padSeqPull       = $padSeqSecond;   
      $padSeqActionName = $padSeqFirst;

      if ( $padSeqFirstParm and $padSeqFirstParm !== TRUE and ! $padSeqParm )
        $padSeqParm = $padSeqFirstParm;
    }

  if ( $padSeqPull ) {   
    $padSeqDone [] = $padSeqFirst;
    $padSeqDone [] = $padSeqSecond;
  }

?>