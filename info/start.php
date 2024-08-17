<?php
 
  if ( ! $padInfo )
    return;

  set_time_limit ( 15 );

  $padInfoCnt++;

  include_once '/pad/info/_lib.php';

  padInfoSet ();

  if ( $padInfoTrack ) include '/pad/info/track/start.php';
  if ( $padInfoStats ) include '/pad/info/stats/start.php';
  if ( $padInfoXapp  ) include '/pad/info/xapp/start.php';
  if ( $padInfoTrace ) include '/pad/info/trace/start.php';
  if ( $padInfoXml   ) include '/pad/info/xml/start.php';
  if ( $padInfoXref  ) include '/pad/info/xref/start.php';
  if ( $padInfoXref  ) include '/pad/info/xapp/start.php';

?>