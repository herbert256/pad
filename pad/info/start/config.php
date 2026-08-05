<?php

  // Starts the $padInfo reporting for the whole request. Reached from inits/info.php once the
  // config/info/<mode>.php file has set the individual switches; returns at once when $padInfo
  // is empty, so a normal request pays nothing.
  //
  // Raises the nesting counter $padInfoCnt, loads the shared helpers, defaults every unset
  // switch to FALSE (padInfoSet, pad/lib/info.php) and starts each enabled mode. $padInfoStarted
  // tells lib/exit.php that info/end/config.php still has to run at the end of the request.

  if ( ! $padInfo )
    return;

  $padInfoCnt++;

  include_once PAD . 'info/_lib/_lib.php';

  padInfoSet ();

  if ( $padInfoTrack ) include PAD . 'info/types/track/start.php';
  if ( $padInfoStats ) include PAD . 'info/types/stats/start.php';
  if ( $padInfoXref  ) include PAD . 'info/types/xref/start.php';
  if ( $padInfoTrace ) include PAD . 'info/types/trace/start.php';
  if ( $padInfoXml   ) include PAD . 'info/types/xml/start.php';

  $padInfoStarted = TRUE;

?>