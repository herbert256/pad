<?php

   if ( ! $GLOBALS ['padInfo'] )
      return;

  if ( $GLOBALS ['padInfoXml']   ) include 'info/types/xml/end.php';
  if ( $GLOBALS ['padInfoXref']  ) include 'info/types/xref/end.php';
  if ( $GLOBALS ['padInfoXapp']  ) include 'info/types/xapp/end.php';
  if ( $GLOBALS ['padInfoStats'] ) include 'info/types/stats/end.php';
  if ( $GLOBALS ['padInfoTrace'] ) include 'info/types/trace/end.php';
  if ( $GLOBALS ['padInfoTrack'] ) include 'info/types/track/end.php';

  $GLOBALS ['padInfoCnt']--;

?>