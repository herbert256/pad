<?php
    
  foreach ( $padSeqPlays as $padK => $padV ) {

    $padSeqPlays [$padK] ['padSeqBuild'] = padSeqBuild ( $padV ['padSeqSeq'], $padSeqCheck );
    $padSeqPlays [$padK] ['padSeqPlay']  = $padSeqCheck;

    return;

  }

?>