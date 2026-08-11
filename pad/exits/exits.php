<?php

  // Turns the finished root-level result into the response.
  //
  // Unescapes it into $padOutput, tidies it when asked, derives the ETag the response
  // will carry, stores the page in the server cache when caching is on, and hands over to
  // exits/output.php, which emits it and ends the request. Reached from start/pad/go.php
  // as the last step of a normal request.

  // An @content@ still standing when the page is done was merged into by nothing. Checked
  // before the unescape, so a marker {ignore} protected - documentation showing it - is
  // still wearing its &at; entities and stays out of the verdict.

  if ( $padCheckSyntax and str_contains ( $padResult [0], '@content@' ) )
    padError ( 'an @content@ stands where nothing merges content into it' );

  $padOutput = padUnescape ( $padResult [0] );

  // The marker gets tidy.php a look even with both switches off: it is consumed (and
  // recorded on the xref) in there, and a response that skipped the file shipped @tidy@
  // to the browser whenever tidying was off.

  if ( $padTidy or $padMyTidy or str_contains ( $padOutput, '@tidy@' ) )
    include PAD . 'exits/tidy.php';

  $padEtag = padMD5 ($padOutput);
  $padStop = 200;

  if ( $padCache and $padCacheServerAge )
    include PAD . 'cache/exits.php';

  include PAD . 'exits/output.php';

?>