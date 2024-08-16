<?php
 
  if ( ! $padInfo )
    return;

  $padInfCnt++;

  include_once '/pad/info/_lib.php';

  padInfoSet ();

  if ( $padInfTrack ) include '/pad/info/types/track/start.php';
  if ( $padInfStats ) include '/pad/info/types/stats/start.php';
  if ( $padInfXapp  ) include '/pad/info/types/xapp/start.php';
  if ( $padInfTrace ) include '/pad/info/types/trace/start.php';
  if ( $padInfXml   ) include '/pad/info/types/xml/start.php';
  if ( $padInfXref  ) include '/pad/info/types/xref/start.php';

?>