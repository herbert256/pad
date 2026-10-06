<?php

  // Settings for $padOutputType 'csv', a data answer (lib/expose.php): the response is the
  // first list the page names in $padExpose, as comma separated values with a header row,
  // written by exits/output/csv.php. Chosen in an application's config, or per request by
  // build/expose.php for ?page&padFormat=csv and Accept: text/csv.
  //
  // Sent like a web page - headers, ETag, 304 - with the web type's two switches defaulted
  // when the request never loaded them; tidy knows only HTML and stays off.

  $padWebNoHeaders = $padWebNoHeaders ?? FALSE;
  $padWebEtag304   = $padWebEtag304   ?? TRUE;
  $padContentType  = 'text/csv; charset=UTF-8';

  $padTidy   = FALSE;
  $padMyTidy = FALSE;

?>
