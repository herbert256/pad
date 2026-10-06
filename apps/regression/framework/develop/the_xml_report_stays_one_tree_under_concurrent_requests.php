<?php

  // The xml info mode wrote its report a line at a time, each line appended on its own, so
  // two requests for one page at the same moment wove their trees into each other - 24 at
  // once left 106 lines where one request writes 23, and no XML parser would read it. The
  // probe of regression/info is fetched twelve times together, and its report must be one
  // well-formed tree afterwards.

  $xmlFile = DATA . '_xml/compact/include/probe.xml';
  $urls    = array_fill ( 0, 12, $padHost . 'regression/info/?probe&padInclude' );

  padCurlMulti ( $urls, 12 );

  // The report is written after each response has gone back.

  for ( $settle = 0; $settle < 20; $settle++ ) {
    usleep ( 100000 );
    clearstatcache ();
  }

  libxml_use_internal_errors ( TRUE );

  $xmlOne = ( file_exists ( $xmlFile ) and simplexml_load_string ( file_get_contents ( $xmlFile ) ) !== FALSE ) ? 'yes' : 'NO';

?>
