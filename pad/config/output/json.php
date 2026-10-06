<?php

  // Settings for $padOutputType 'json', a data answer (lib/expose.php): the response is the
  // variables the page names in $padExpose, as one JSON object, written by
  // exits/output/json.php. Chosen in an application's config for an application that is an
  // API, or per request by build/expose.php for ?page&padFormat=json and Accept:
  // application/json.
  //
  // The body travels the way the web type sends a page - headers, ETag, 304 - so the web
  // type's two switches get their defaults when the request never loaded them, and keep
  // what they were when it switches over from web. Tidy knows only HTML and stays off.

  $padWebNoHeaders = $padWebNoHeaders ?? FALSE;
  $padWebEtag304   = $padWebEtag304   ?? TRUE;
  $padContentType  = 'application/json; charset=UTF-8';

  $padTidy   = FALSE;
  $padMyTidy = FALSE;

?>
