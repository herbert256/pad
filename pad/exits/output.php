<?php

  // Emits $padOutput through the writer for the configured $padOutputType - web, file,
  // download or console - and then ends the request with padExit ($padStop).
  //
  // Reached both from exits/exits.php with a freshly built page and from cache/hit.php
  // with a page taken from the cache; in the latter case the body may still be gzipped,
  // which only the web writer can pass on as is, so the other writers get it unzipped.

  $padLen = ( $padStop == 200 ) ? strlen ( $padOutput ) : 0;

  // The reference's output-type family records here, while the type that actually writes
  // this output is the active one - a file-writing page hands the request back to 'web'
  // before the exit, so recording any later tells the wrong story.

  if ( ( $padInfoXref ?? FALSE ) and function_exists ( 'padInfoXref' )
       and $padOutputType != ( $padConfigSet ['outputType'] ?? $padConfigDefault ['outputType'] ) )
    padInfoXref ( 'config/outputType', $padOutputType );

  padCheckBuffers ();

  // A coverage record is written before the body goes out, not at the exit after it: the
  // client has the whole page once it is sent, and a test that reads the record right after
  // its fetch found it not written yet (lib/coverage.php). padExit writes it for a request
  // that ends any other way.

  if ( $padCoverageRun and ! padSecondTime ( 'exitCoverage' ) )
    padCoverageWrite ( $padStop );

  // A GET this application records for replay is kept with the answer it is about to get
  // (lib/replay.php) - the same moment, for the same reason.

  if ( ( $padRecord or isset ( $_REQUEST ['padRecord'] ) ) and ! padSecondTime ( 'exitRecord' ) )
    padReplayRecord ( $padStop );

  if ( $padOutputType != 'web' and $padCacheStop == 200 and $padCacheServerGzip )
    $padOutput = padUnzip ( $padOutput );

  // The debug toolbar and the live reload script of a local page go in on the way out -
  // after the page cache has stored the page, so a cached copy never carries them
  // (lib/toolbar.php, lib/reload.php).

  if ( $padToolbar )
    padToolbarAdd ();

  if ( $padReload )
    padReloadAdd ();

  include PAD . "exits/output/$padOutputType.php";

  padExit ( $padStop );

?>
