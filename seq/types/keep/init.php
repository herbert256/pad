<?php

  $padSeqFilter = [];

  foreach ( $padPrm [$pad] as $padSeqFilterName => $padSeqFilterVal )

    if ( $padSeqFilterName <> $padSeqSeq and file_exists ( "/pad/seq/types/$padSeqFilterName/bool.php" ) ) {

      $padSeqFilter [$padSeqFilterName] = $padSeqFilterVal;

      include_once "/pad/seq/types/$padSeqFilterName/bool.php";

      if ( isset ( $padSeqOprGo [$padSeqFilterName] ) )
        unset ( $padSeqOprGo [$padSeqFilterName] );

      padDone ( $padSeqFilterName );

    }

?>