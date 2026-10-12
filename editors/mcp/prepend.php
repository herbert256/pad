<?php

  // Loaded ahead of editors/render.php by the MCP server (php -d auto_prepend_file=...), in
  // this checkout and in the snapshot of another commit pad_compare renders on alike: it
  // asks nothing of the engine, so an old engine runs under it as well as the current one.
  //
  // At the very end of the request - after the engine, and after the shutdown functions the
  // engine registered, since this one registers itself only once shutdown has begun - it
  // writes one trailer line the server takes off the output:
  //
  //   \x1ePAD-MCP\x1e{"status":200,"php":{"output":"...","result":"...","json":...,"error":"..."}}
  //
  // status   the HTTP status the request ended with - the command line has no headers to
  //          read it from, but http_response_code() keeps what the engine set
  // php      for pad_php, pad_case and pad_compare: the PHP file PAD_MCP_CODE names, run in
  //          the booted application - the engine's library, the application's _lib, its
  //          configuration and database - with what it echoed, what it returned (as
  //          var_export, and as JSON when it is data), or what it threw. A warning, a
  //          notice and a padError are thrown too: a snippet that tries a helper wants to
  //          see them, and the request would otherwise end on a padError.

  register_shutdown_function ( function () {

    register_shutdown_function ( function () {

      while ( ob_get_level () )
        if ( ! @ ob_end_flush () )
          break;

      $mcpTrailer = [ 'status' => http_response_code () ?: 200 ];

      $mcpCode = getenv ( 'PAD_MCP_CODE' );

      if ( $mcpCode ) {

        $mcpPhp = [ 'output' => '' ];

        set_error_handler ( function ( $no, $message, $file, $line ) {
          if ( ! ( error_reporting () & $no ) )
            return FALSE;
          throw new ErrorException ( $message, 0, $no, $file, $line );
        } );

        // padError ends a request under every error action - the snippet's answer with it. The
        // engine's own probes that must survive one - the health check's - raise it as an
        // exception instead while padHealthBusy is set (lib/error.php, padErrorHook): set here
        // for the snippet, a helper's report of a wrong argument is what the snippet answers.

        $mcpBusy = $GLOBALS ['padHealthBusy'] ?? NULL;
        $GLOBALS ['padHealthBusy'] = TRUE;

        ob_start ();

        try {

          $mcpResult = ( function () use ( $mcpCode ) { return include $mcpCode; } ) ();

          if ( $mcpResult !== NULL ) {
            $mcpPhp ['result'] = var_export ( $mcpResult, TRUE );
            $mcpJson = json_encode ( $mcpResult, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR );
            if ( $mcpJson !== FALSE )
              $mcpPhp ['json'] = json_decode ( $mcpJson );
          }

        } catch ( Throwable $mcpError ) {

          $mcpPhp ['error'] = get_class ( $mcpError ) . ': ' . $mcpError->getMessage ()
                            . ' in ' . $mcpError->getFile () . ':' . $mcpError->getLine ();

        }

        while ( ob_get_level () )
          $mcpPhp ['output'] = ob_get_clean () . $mcpPhp ['output'];

        $GLOBALS ['padHealthBusy'] = $mcpBusy;

        restore_error_handler ();

        $mcpTrailer ['php'] = $mcpPhp;

      }

      echo "\n\x1ePAD-MCP\x1e", json_encode ( $mcpTrailer, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR ), "\n";

    } );

  } );

?>
