<?php

  // Records what a page answers now as its prediction - the deliberate, one-test-at-a-time
  // descendant of the retired accept-all flow. Only the covering suites record: their
  // stores were bootstrapped from verified output, and refreshing one is the same act. The
  // handwritten suites - Pages, Common, Errors, Framework - stay out, because an answer
  // there encodes intent, and intent is not recorded. A pattern or an HTTP answer is
  // handwritten wherever it lives, so those refuse too.
  //
  // Recording is a two-step: the first visit shows what stands against what the page
  // answers now and does not write; the confirm link carries a hash of the shown body, and
  // the write happens only when the refetch still matches it - a page that answers
  // differently between look and click records nothing. Only a test the current result
  // holds as failing or new records at all: the row being acted on must be the row that
  // was seen.

  $recSuite = padMakeSafe ( $suite ?? '', 40 );
  $recName  = $name ?? '';

  $recEntry = getSuites () [$recSuite] ?? [];

  if ( ! isset ( $recEntry ['over'] ) )
    return padError ( "the suite '$recSuite' keeps handwritten answers - nothing records there" );

  if ( ! preg_match ( '#^[a-zA-Z0-9_][a-zA-Z0-9_/-]*$#', $recName ) )
    return padError ( "there is no test named '$recName'" );

  $recRow = '';

  foreach ( getSuiteLast ( $recSuite ) ['tests'] as $recOne )
    if ( $recOne ['name'] == $recName )
      $recRow = $recOne ['status'];

  if ( $recRow != 'FAILED' and $recRow != 'new' )
    return padError ( "'$recName' is not failing in the current $recSuite result - record acts only on the row that was seen" );

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

  $recNew  = trim ( $recCurl ['data'] );
  $recHash = substr ( md5 ( $recNew ), 0, 12 );

  if ( ( $go ?? '' ) != $recHash ) {

    // The preview: what stands, what the page answers now, and the one link that records
    // it - carrying the hash of exactly this body.

    echo '<h2>record ' . htmlspecialchars ( "$recSuite / $recName" ) . '</h2>';
    echo '<p>The store holds:</p><pre>'   . htmlspecialchars ( $recOld ) . '</pre>';
    echo '<p>The page answers:</p><pre>'  . htmlspecialchars ( $recNew ) . '</pre>';
    echo '<p><a href="?record&suite=' . urlencode ( $recSuite ) . '&name=' . urlencode ( $recName )
       . '&go=' . $recHash . '">record this answer</a></p>';

    return;

  }

  // The store lives under apps/, not DATA, so the write is native - padFilePut anchors
  // everything it is handed to the data tree.

  if ( ! is_dir ( dirname ( $recWant ) ) )
    mkdir ( dirname ( $recWant ), 0755, TRUE );

  file_put_contents ( $recWant, $recNew );

  padRedirect ( "$recSuite/index&test" );

?>