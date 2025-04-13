<?php
    
  foreach ( $padSeqPlays as $padK => $padV )

    if ( $padV ['padSeqPlay'] == 'make' ) {

      $padSeqPlays [$padK] ['padSeqBuild']       = padSeqBuild ( $padV ['padSeqSeq'], $padSeqCheck );
      $padSeqPlays [$padK] ['padSeqPlay']        = $padSeqCheck;

      return;

    }

?>