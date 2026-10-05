<?php

  // Settles the designer preview for this request (lib/sample.php): $padSampleMode is ''
  // for a normal request, 'use' when ?padSample asks for the page's sample data and
  // $padSample allows it, 'capture' when ?padSample=capture records it. Either way the
  // page cache stays out - a sample rendering must never be served as the real page, nor
  // a capture be answered from the cache without running the PHP it records.

  $padSampleMode = padSampleMode ();
  $padSampleData = NULL;

  if ( $padSampleMode )
    $padCache = FALSE;

?>
