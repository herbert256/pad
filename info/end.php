<?php

   if ( ! $GLOBALS ['padInfo'] )
      return;

  if ( $GLOBALS ['padInfoXml']   ) include '/pad/info/xml/end.php';
  if ( $GLOBALS ['padInfoXref']  ) include '/pad/info/xref/end.php';
  if ( $GLOBALS ['padInfoXapp']  ) include '/pad/info/xapp/end.php';
  if ( $GLOBALS ['padInfoStats'] ) include '/pad/info/stats/end.php';
  if ( $GLOBALS ['padInfoTrace'] ) include '/pad/info/trace/end.php';
  if ( $GLOBALS ['padInfoTrack'] ) include '/pad/info/track/end.php';

  $GLOBALS ['padInfoCnt']--;

?>