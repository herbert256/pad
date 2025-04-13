<?php

  if ( $padSeqPrefix )

    if  ( padSeqPlay ( $padSeqPrefix ) and file_exists ( "sequence/actions/types/$padSeqTag.php"    ) ) {

      $padSeqActionAfterName = $padSeqTag;
      $padSeqActionAfterParm = $padSeqParm;

    } elseif ( padSeqPlay ( $padSeqTag ) and file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) )  {

      $padSeqActionAfterName = $padSeqPrefix;
      $padSeqActionAfterParm = $padSeqParm;

    }
    
?>