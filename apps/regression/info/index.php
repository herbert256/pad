<?php

  // Fetches the probe - a loop, a pipe and a sequence, rendered with all five info modes on
  // and every option of each - and asserts that every mode recorded something for that very
  // request: not that artifacts exist, but that this fetch grew them.
  //
  // The xml report is the exception: each request writes it afresh. A stale tree is left
  // in its place before the fetch, so afterwards the file has to be one well-formed tree
  // without it - an engine that stops clearing the report appends to the stale tree and
  // the two roots no longer parse.

  $traceBefore = count ( glob ( DATA . 'trace/probe/*' ) ?: [] );
  $trackBefore = count ( glob ( DATA . 'track/requests/*' ) ?: [] );
  $xmlFile     = DATA . '_xml/compact/include/probe.xml';
  $dbBefore    = (int) padDb ( "field count(*) from track_request" );

  padFilePut ( $xmlFile, "<stale />" );

  libxml_use_internal_errors ( TRUE );

  $r = padCurl ( $padHost . 'regression/info/?probe&padInclude' );

  $stats = json_decode ( $r ['headers'] ['PAD-Stats'] ?? '', TRUE );

  // The recorders finish after the probe's response has already been flushed back, so the
  // growth is given a moment to land before it is read.

  for ( $settle = 0; $settle < 20; $settle++ ) {

    clearstatcache ();

    $traceAfter = count ( glob ( DATA . 'trace/probe/*' ) ?: [] );
    $trackAfter = count ( glob ( DATA . 'track/requests/*' ) ?: [] );
    $xmlText    = file_exists ( $xmlFile ) ? file_get_contents ( $xmlFile ) : '';
    $xmlOne     = ! str_contains ( $xmlText, '<stale' )
                  and simplexml_load_string ( $xmlText ) !== FALSE;
    $dbAfter    = (int) padDb ( "field count(*) from track_request" );

    if ( $traceAfter > $traceBefore and $trackAfter > $trackBefore
         and $xmlOne and $dbAfter > $dbBefore )
      break;

    usleep ( 100000 );

  }

  $xref = padFileGet ( DATA . 'reference/tag/pad/sequence.txt' );

  $vStats = ( is_array ( $stats ) and isset ( $stats ['total'] ) )      ? 'yes' : 'NO';
  $vTrace = ( $traceAfter > $traceBefore )                              ? 'yes' : 'NO';
  $vTrack = ( $trackAfter > $trackBefore and $dbAfter > $dbBefore )     ? 'yes' : 'NO';
  $vXml   = ( $xmlOne )                                                 ? 'yes' : 'NO';
  $vXref  = ( str_contains ( $xref, 'regression/info;probe' ) )         ? 'yes' : 'NO';

?>