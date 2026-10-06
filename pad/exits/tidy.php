<?php

  // Cleans up $padOutput just before it is sent: real HTML Tidy when $padTidy is set or
  // the page carries a @tidy@ marker, otherwise PAD's own lightweight pass in
  // exits/myTidy.php.
  //
  // Skipped for padInclude, padExamples and padReference requests and for a response
  // fragment, whose output is a part of a page that has to stay exactly as it was built. The @tidy@ marker is consumed
  // either way - CONSTRUCTS.md says it is removed, and until it was, every response that
  // carried it shipped the marker to the browser.

  $padTidyMarked = strpos ( $padOutput, '@tidy@' ) !== FALSE;

  if ( $padTidyMarked )
    $padOutput = str_replace ( '@tidy@', '', $padOutput );

  // This is the one place @tidy@ is ever consumed, so it is where the construct gets on
  // the xref record - before the bare-render returns below, which a harvest request takes.

  global $padInfoXref;

  if ( $padTidyMarked and ( $padInfoXref ?? FALSE ) )
    padInfoXref ( 'constructs', 'tidy' );

  if ( isset ( $_REQUEST ['padInclude']   ) ) return;
  if ( $padFragmentSent ?? FALSE            ) return;
  if ( padSelfSwitch ( 'padExamples'  ) ) return;
  if ( padSelfSwitch ( 'padReference' ) ) return;

  // Both passes know HTML and nothing else. Run over a page that declared another content
  // type - JSON, CSV, plain text - tidy wrapped it in <html><head><body> and the client got
  // a broken document of the type it was promised.

  if ( ! str_starts_with ( strtolower ( trim ( $padContentType ?? '' ) ), 'text/html' ) )
    return;

  include PAD . 'config/tidy.php';

  if ( $padTidy or $padTidyMarked )

    $padOutput = padTidy ( $padOutput );

  elseif ( $padMyTidy )

    include PAD . 'exits/myTidy.php';

?>
