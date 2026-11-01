<?php

  // Settings for $padOutputType 'format', a data answer (lib/expose.php) in a format of the
  // formats subsystem other than JSON and CSV - an iCalendar, a spreadsheet, a feed, TOML:
  // the variables the page names in $padExpose, written by exits/output/format.php in the
  // format $padOutputFormat names. Chosen per request by build/expose.php for
  // ?page&padFormat=ics and an Accept header that asks for the format's media type, or in an
  // application's config with $padOutputFormat beside it.
  //
  // The content type is the format's own, with the charset of a text format; the body
  // travels the way the web type sends a page - headers, ETag, 304 - so the web type's two
  // switches get their defaults when the request never loaded them. Tidy knows only HTML.

  $padOutputFormatDef = padFormatDef ( padFormatFind ( (string) ( $padOutputFormat ?? '' ), 'write' ) );

  $padWebNoHeaders = $padWebNoHeaders ?? FALSE;
  $padWebEtag304   = $padWebEtag304   ?? TRUE;
  $padContentType  = ( $padOutputFormatDef ['mime'] ?? 'text/plain' ) . ( ( $padOutputFormatDef ['binary'] ?? FALSE ) ? '' : '; charset=UTF-8' );

  $padTidy   = FALSE;
  $padMyTidy = FALSE;

?>
