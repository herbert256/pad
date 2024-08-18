<?php
 
  if ( ! $padInfo )
    return;

  set_time_limit ( 15 );

  $padInfoCnt++;

  include_once '/pad/info/_lib/_lib.php';

  padInfoSet ();

  if ( $padInfoTrack ) include '/pad/info/types/track/start.php';
  if ( $padInfoStats ) include '/pad/info/types/stats/start.php';
  if ( $padInfoXapp  ) include '/pad/info/types/xapp/start.php';
  if ( $padInfoTrace ) include '/pad/info/types/trace/start.php';
  if ( $padInfoXml   ) include '/pad/info/types/xml/start.php';
  if ( $padInfoXref  ) include '/pad/info/types/xref/start.php';
  if ( $padInfoXref  ) include '/pad/info/types/xapp/start.php';

?>