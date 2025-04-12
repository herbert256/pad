<?php

  if ( $padSeqInitType == 'tag' and padSeqPlay ($padSeqInitValue ) and $padSeqInitValue <> 'make' )

    if ( in_array ( $padSeqBuild, ['start','store','pull'] ) ) {

      foreach ( $padSeqPlays as $padSeqPlay )
        if ( $padSeqPlay ['padSeqPlay'] == $padSeqInitValue )
          return;

      foreach ( $padSeqPlays as $padK => $padV )

        if ( $padV ['padSeqPlay'] == 'make' ) {

          $padSeqPlays [$padK] ['padSeqBuild']       = padSeqBuild ( $padV ['padSeqSeq'], $padSeqInitValue );
          $padSeqPlays [$padK] ['padSeqPlay']        = $padSeqInitValue;
          $padSeqPlays [$padK] ['padSeqPlaySource'] .= '|inits/check/tag';

          return;

        }

  }

?>