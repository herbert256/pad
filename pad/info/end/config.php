<?php

  // Closes off the $padInfo reporting for the request: the counterpart of info/start/config.php,
  // run once from padExitGo (pad/lib/exit.php) when $padInfoStarted is set.
  //
  // Ends each enabled mode - xml writes its tree, stats measures the timings, trace closes and
  // prunes its directories, track files the request - and drops $padInfoCnt back. Order differs
  // from the start file on purpose: trace ends after stats so the timings appear in the trace.

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