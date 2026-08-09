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

  if ( $padOutputType != 'web' and $padCacheStop == 200 and $padCacheServerGzip )
    $padOutput = padUnzip ( $padOutput );

  include PAD . "exits/output/$padOutputType.php";

  padExit ( $padStop );

?>