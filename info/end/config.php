<?php

   if ( ! $GLOBALS ['padInfo'] )
      return;

  if ( $GLOBALS ['padInfoXml']   ) include '/pad/info/types/xml/end.php';
  if ( $GLOBALS ['padInfoXref']  ) include '/pad/info/types/xref/end.php';
  if ( $GLOBALS ['padInfoXapp']  ) include '/pad/info/types/xapp/end.php';
  if ( $GLOBALS ['padInfoStats'] ) include '/pad/info/types/stats/end.php';
  if ( $GLOBALS ['padInfoTrace'] ) include '/pad/info/types/trace/end.php';
  if ( $GLOBALS ['padInfoTrack'] ) include '/pad/info/types/track/end.php';

  $GLOBALS ['padInfoCnt']--;

?>