<?php

  $padSeqFilter = [];

  foreach ( $padPrm [$pad] as $padSeqFilterName => $padSeqFilterVal )

    if ( $padSeqFilterName <> $padSeqSeq and file_exists ( "$padSeqTypes/$padSeqFilterName/bool.php" ) ) {

      $padSeqFilter [$padSeqFilterName] = $padSeqFilterVal;

      include_once "$padSeqTypes/$padSeqFilterName/bool.php";

      if ( isset ( $padSeqOprGo [$padSeqFilterName] ) )
        unset ( $padSeqOprGo [$padSeqFilterName] );

      padDone ( $padSeqFilterName );

    }

?>