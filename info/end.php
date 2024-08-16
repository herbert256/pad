<?php

   if ( ! $padInfo )
      return;

  if ( $padInfStats ) include '/pad/info/types/stats/end.php';
  if ( $padInfXml   ) include '/pad/info/types/xml/end.php';
  if ( $padInfXref  ) include '/pad/info/types/xref/end.php';
  if ( $padInfTrack ) include '/pad/info/types/track/end.php';
  if ( $padInfTrace ) include '/pad/info/types/trace/end.php';
  if ( $padInfXapp  ) include '/pad/info/types/xapp/end.php';

  $padInfCnt--;

?>