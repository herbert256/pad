<?php

  // Turns the finished root-level result into the response.
  //
  // Unescapes it into $padOutput, tidies it when asked, hands it to the application's
  // output hook when it has one, derives the ETag the response will carry, stores the page
  // in the server cache when caching is on, and hands over to exits/output.php, which
  // emits it and ends the request. Reached from start/pad/go.php as the last step of a
  // normal request.

  // A request for one response fragment that the page never rendered - lib/respond.php.

  padFragmentMissing ();

  // An @content@ still standing when the page is done was merged into by nothing. Checked
  // before the unescape, so a marker {ignore} protected - documentation showing it - is
  // still wearing its &at; entities and stays out of the verdict.

  if ( $padCheckSyntax and str_contains ( $padResult [0], '@content@' ) )
    padErrorAt ( 'an @content@ stands where nothing merges content into it', [ 'search' => '@content@' ] );

  // The values' stand-ins come back too - before tidy, which must see the quotes and = of
  // the markup a tag answered. Always, not only under $padProtectValues: a stand-in that
  // arrived in the input is a character, and a page written with the switch on in one
  // place and off in another restores alike.

  // The {stack} markers are filled in now that every {push} of the page has been made -
  // lib/stack.php - still in the engine's own encoding, as the pushes were rendered in it.
  // After a {flush} only the part of the page that has not gone out yet is left to send
  // (lib/flush.php); that part is cut from the raw result, which the flushed text is a
  // prefix of, before the stacks are filled.

  $padFlushLeft  = padFlushRest ( $padResult [0] );
  $padResult [0] = padStackFill ( $padResult [0] );

  $padOutput = padUnprotect ( padUnescape ( padStackFill ( $padFlushLeft ) ) );

  // With $padCsrf on, each form of the page that posts back here carries the session's
  // token without the template having to ask (lib/csrf.php) - the forms an application
  // already had are protected by the one setting. A token that went into the page with
  // $padCsrf off - a {form} writes one - is taken out of a form whose button sends it to
  // another site, or by GET.

  if ( ( $padCsrf or isset ( $padCsrfIssued ) ) and $padOutputType == 'web' )
    $padOutput = padCsrfForms ( $padOutput, $padCsrf );

  // A live request - a {live} region posting an event - is answered with that region's
  // content alone, without tidy, which would make a page of it (lib/live.php).

  if ( padLive () !== '' )
    $padOutput = padLiveAnswer ();

  // A {toc} is filled in once every heading of the page is written, the answer of a live
  // request included - lib/toc.php.

  $padOutput = padTocFill ( $padOutput );

  // A page with a {pdf} answers the document of what it rendered instead (lib/pdf.php).

  if ( padPdfAsked () )
    $padOutput = padPdfAnswer ( $padOutput );

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

  // A data answer (lib/expose.php) is made here, where the page's rendering stood, so the
  // output hook below sees what goes out: the writers made it after the hook, which was
  // handed the empty rendering of the templates a data answer skips, and the JSON or CSV
  // went out past it.

  if ( $padOutputType == 'json' )
    $padOutput = padExposeJson ();
  elseif ( $padOutputType == 'csv' )
    $padOutput = padExposeCsv ();

  // The application's _events/output.php sees the page as it will go out and may change
  // it - before the ETag and the page cache, so both describe what is actually sent.

  if ( padEventCheck ( 'output' ) )
    $padOutput = (string) padEvent ( 'output', [ 'output' => $padOutput ] ) ['output'];

  $padEtag = padMD5 ($padOutput);
  $padStop = 200;

  if ( $padCache and $padCacheServerAge )
    include PAD . 'cache/exits.php';

  // A capture of sample data (lib/sample.php) is written once the page has rendered, so the
  // answers of the named database tags in its template are in it as well.

  if ( $padSampleMode == 'capture' )
    padSampleWrite ();

  include PAD . 'exits/output.php';

?>
