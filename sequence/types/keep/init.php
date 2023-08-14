<?php

  $padSeqFilter = [];

  foreach ( $padPrm [$pad] as $padSeqFilterName => $padSeqFilterVal ) {

    if ( $padSeqFilterName <> $padSeqSeq and padExists ( "$padSeqTypes/$padSeqFilterName/bool.php" ) ) {

      include_once "$padSeqTypes/$padSeqFilterName/bool.php";

      $padSeqFilter [$padSeqFilterName] = $padSeqFilterVal;

      if ( isset ( $padSeqOprGo [$padSeqFilterName] ) )
        unset ( $padSeqOprGo [$padSeqFilterName] );

    }

  }

?>