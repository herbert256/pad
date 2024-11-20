<?php
 
  if ( ! $padInfo )
    return;

  $padInfoCnt++;

  include_once PAD . 'info/_lib/_lib.php';

  padInfoSet ();

  if ( $padInfoTrack ) include 'info/types/track/start.php';
  if ( $padInfoStats ) include 'info/types/stats/start.php';
  if ( $padInfoXapp  ) include 'info/types/xapp/start.php';
  if ( $padInfoTrace ) include 'info/types/trace/start.php';
  if ( $padInfoXml   ) include 'info/types/xml/start.php';
  if ( $padInfoXref  ) include 'info/types/xref/start.php';
  if ( $padInfoXref  ) include 'info/types/xapp/start.php';

?>