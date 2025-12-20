<?php

  global $padInfo, $padInfoCnt, $padInfoStats, $padInfoTrace, $padInfoTrack, $padInfoXml, $padInfoXref;

   if ( ! $padInfo )
      return;

  if ( $padInfoXml   ) include PAD . 'info/types/xml/end.php';
  if ( $padInfoXref  ) include PAD . 'info/types/xref/end.php';
  if ( $padInfoStats ) include PAD . 'info/types/stats/end.php';
  if ( $padInfoTrace ) include PAD . 'info/types/trace/end.php';
  if ( $padInfoTrack ) include PAD . 'info/types/track/end.php';

  $padInfoCnt--;

?>