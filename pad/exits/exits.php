<?php

  // Turns the finished root-level result into the response.
  //
  // Unescapes it into $padOutput, tidies it when asked, hands it to the application's
  // output hook when it has one, derives the ETag the response will carry, stores the page
  // in the server cache when caching is on, and hands over to exits/output.php, which
  // emits it and ends the request. Reached from start/pad/go.php as the last step of a
  // normal request.

  // An @content@ still standing when the page is done was merged into by nothing. Checked
  // before the unescape, so a marker {ignore} protected - documentation showing it - is
  // still wearing its &at; entities and stays out of the verdict.

  if ( $padCheckSyntax and str_contains ( $padResult [0], '@content@' ) )
    padError ( 'an @content@ stands where nothing merges content into it' );

  // The values' stand-ins come back too - before tidy, which must see the quotes and = of
  // the markup a tag answered. Always, not only under $padProtectValues: a stand-in that
  // arrived in the input is a character, and a page written with the switch on in one
  // place and off in another restores alike.

  $padOutput = padUnprotect ( padUnescape ( $padResult [0] ) );

  // With $padCsrf on, each form of the page that posts back here carries the session's
  // token without the template having to ask (lib/csrf.php) - the forms an application
  // already had are protected by the one setting.

  if ( $padCsrf and $padOutputType == 'web' )
    $padOutput = padCsrfForms ( $padOutput );

  // The development check of the finished HTML - duplicate ids, images without alt, fields
  // without a label, broken ?page links - for a local request that asks for it, on what the
  // templates wrote, before tidy rearranges it (lib/outputCheck.php).

  if ( padOutputCheckOn () )
    $padOutput = padOutputCheckPage ( $padOutput );

  // The marker gets tidy.php a look even with both switches off: it is consumed (and
  // recorded on the xref) in there, and a response that skipped the file shipped @tidy@
  // to the browser whenever tidying was off.

  if ( $padTidy or $padMyTidy or str_contains ( $padOutput, '@tidy@' ) )
    include PAD . 'exits/tidy.php';

  // The application's _events/output.php sees the page as it will go out and may change
  // it - before the ETag and the page cache, so both describe what is actually sent.

  if ( padEventCheck ( 'output' ) )
    $padOutput = (string) padEvent ( 'output', [ 'output' => $padOutput ] ) ['output'];

  $padEtag = padMD5 ($padOutput);
  $padStop = 200;

  if ( $padCache and $padCacheServerAge )
    include PAD . 'cache/exits.php';

  include PAD . 'exits/output.php';

?>