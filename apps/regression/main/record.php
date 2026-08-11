<?php

  // Records what a page answers now as its prediction - the deliberate, one-test-at-a-time
  // descendant of the retired accept-all flow, offered on the failure row itself with the
  // diff in view. Only the covering suites record: their stores were bootstrapped from
  // verified output, and refreshing one is the same act. The handwritten suites - Pages,
  // Common, Framework - stay out, because an answer there encodes intent, and intent is
  // not recorded. A pattern or an HTTP answer is handwritten wherever it lives, so those
  // refuse too; only an exact body, or a test with no answer yet, records.

  $recSuite = padMakeSafe ( $suite ?? '', 40 );
  $recName  = $name ?? '';

  $recEntry = getSuites () [$recSuite] ?? [];

  if ( ! isset ( $recEntry ['over'] ) )
    return padError ( "the suite '$recSuite' keeps handwritten answers - nothing records there" );

  if ( ! preg_match ( '#^[a-zA-Z0-9_][a-zA-Z0-9_/-]*$#', $recName ) )
    return padError ( "there is no test named '$recName'" );

  $recWant = APPS . $recEntry ['store'] . "/$recName.txt";
  $recOld  = trim ( padFileGet ( $recWant ) );

  if ( str_starts_with ( $recOld, 'HTTP ' )
       or ( strlen ( $recOld ) > 1 and str_starts_with ( $recOld, '/' ) and str_ends_with ( $recOld, '/' ) ) )
    return padError ( "the answer for '$recName' is handwritten - a pattern refreshes only deliberately" );

  // The test name is the store path; padAppBoundary() draws the application inside it
  // with the same rule the walkers apply.

  list ( $recApp, $recItem ) = padAppBoundary ( $recEntry ['strip'] . $recName );

  $recCurl = padCurl ( getSuiteUrl ( $recApp, $recItem ) );

  if ( ! str_starts_with ( $recCurl ['result'], '2' ) )
    return padError ( "'$recName' answers HTTP " . $recCurl ['result'] . " - a failing page records nothing" );

  // The store lives under apps/, not DATA, so the write is native - padFilePut anchors
  // everything it is handed to the data tree.

  if ( ! is_dir ( dirname ( $recWant ) ) )
    mkdir ( dirname ( $recWant ), 0755, TRUE );

  file_put_contents ( $recWant, trim ( $recCurl ['data'] ) );

  padRedirect ( "$recSuite/index&test" );

?>
