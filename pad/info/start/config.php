<?php

  if ( ! $padInfo )
    return;

  $padInfoCnt++;

  include_once PAD . 'info/_lib/_lib.php';

  padInfoSet ();

  if ( $padInfoTrack ) include PAD . 'info/types/track/start.php';
  if ( $padInfoStats ) include PAD . 'info/types/stats/start.php';
  if ( $padInfoXref  ) include PAD . 'info/types/xref/start.php';
  if ( $padInfoTrace ) include PAD . 'info/types/trace/start.php';
  if ( $padInfoXml   ) include PAD . 'info/types/xml/start.php';

  $padInfoStarted = TRUE;

?>
