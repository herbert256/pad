<?php

  // Machine-readable error output for local tooling: when the request comes from the CLI or
  // from curl on ::1 (padClaudeCheck), padClaudeError answers with a 500 and a JSON body
  // holding the message, file, line, backtrace and every global - bucketed by padClaudeFields
  // into pad (pad*), sequence (pq*), php (_*) and application variables - then exits.
  //
  // Included first of all by start/pad.php, so it is available to the boot net. padBootStop
  // calls padClaudeError ahead of any human-facing output, and inits/error.php uses
  // padClaudeCheck to force $padErrorAction to 'boot' for such requests, keeping this path
  // in charge instead of the configured error action.
  //
  // The command line is recognised by the SAPI rather than by REMOTE_ADDR being absent. A
  // missing REMOTE_ADDR is normal for the CLI but is not proof of it - a server or proxy
  // arrangement that does not set it would have opened this channel, and everything it
  // reports, to whoever asked.

  function padClaudeCheck () {

    if ( PHP_SAPI === 'cli' )
      return TRUE;

    $addr  = $_SERVER ['REMOTE_ADDR']     ?? '';
    $agent = $_SERVER ['HTTP_USER_AGENT'] ?? '';

    // Both loopbacks: localhost resolves to either family, and which one a connection
    // takes is a coin toss under load - the answer must not depend on it.

    if ( in_array ( $addr, [ '::1', '127.0.0.1' ] ) and str_contains ( $agent, 'curl' ) )
      return TRUE;

    return FALSE;

  }

  function padClaudeError ( $error, $file, $line ) {

    if ( padClaudeCheck () ) {

      padClaudeFields ( $app, $pad, $php, $seq );

      $claude ['error']    = $error;
      $claude ['file']     = $file;
      $claude ['line']     = $line;
      $claude ['stack']    = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);
      $claude ['app']      = $app;
      $claude ['pad']      = $pad;
      $claude ['sequence'] = $seq;
      $claude ['php']      = $php;

      header ( 'HTTP/1.0 500 Internal Server Error' );
      header ( 'Content-Type: application/json' );

      // Globals can hold binary (a dump of a .DS_Store, an image body); without these
      // flags one bad string makes json_encode answer FALSE and the channel goes silent.

      echo json_encode ( $claude, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR );

      // The web side of this channel carries the failure in the 500 header; a shell caller
      // reads it from the process status instead.

      exit ( ( PHP_SAPI == 'cli' ) ? 1 : 0 );

    }

  }

  function padClaudeFields ( &$app, &$pad, &$php, &$seq ) {

    $seq = $php = $pad = $app = [];

    foreach ($GLOBALS as $key => $value)

      if ( substr($key, 0, 3)  == 'pad' )
        $pad [$key] = $value;
      elseif ( substr($key, 0, 2)  == 'pq' )
        $seq [$key] = $value;
      elseif ( substr($key, 0, 1)  == '_' )
        $php [$key] = $value;
      else
        $app [$key] = $value;

  }


?>