<?php

  if ( isset ( $_REQUEST ['padReference'] ) ) {

    $padInfo      = 'xref';
    $padInfoTrack = FALSE;
    $padInfoXml   = FALSE;
    $padInfoStats = FALSE;
    $padInfoTrace = FALSE;
    $padInfoXref  = TRUE;

    $padInclude   = TRUE;
    $padTidy      = FALSE;

  }

  if ( $padInfo )
    include PAD . 'info/start/config.php';

?>