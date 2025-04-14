<?php

  if ( $padSeqInitType == 'tag' and padSeqPlay ($padSeqInitValue ) and $padSeqInitValue <> 'make' )

    if ( padSeqStore ( $padSeqBuild ) ) {

      foreach ( $padSeqPlays as $padSeqPlay )
        if ( $padSeqPlay ['padSeqPlay'] == $padSeqInitValue )
          return;

      foreach ( $padSeqPlays as $padK => $padV )

        if ( $padV ['padSeqPlay'] == 'make' ) {

          $padSeqPlays [$padK] ['padSeqBuild']       = padSeqBuild ( $padV ['padSeqSeq'], $padSeqInitValue );
          $padSeqPlays [$padK] ['padSeqPlay']        = $padSeqInitValue;

          return;

        }

  }

?>
