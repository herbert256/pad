<?php

   if ( ! $GLOBALS ['padInfo'] )
      return;

  if ( $GLOBALS ['padInfoXml']   ) include PAD . 'info/types/xml/end.php';
  if ( $GLOBALS ['padInfoXref']  ) include PAD . 'info/types/xref/end.php';
  if ( $GLOBALS ['padInfoStats'] ) include PAD . 'info/types/stats/end.php';
  if ( $GLOBALS ['padInfoTrace'] ) include PAD . 'info/types/trace/end.php';
  if ( $GLOBALS ['padInfoTrack'] ) include PAD . 'info/types/track/end.php';

  $GLOBALS ['padInfoCnt']--;

?>