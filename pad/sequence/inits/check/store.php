<?php
    
  foreach ( $pqPlays as $padK => $padV ) {

    $pqPlays [$padK] ['pqBuild'] = pqBuild ( $padV ['pqSeq'], $pqCheck );
    $pqPlays [$padK] ['pqPlay']  = $pqCheck;

    return;

  }

?>