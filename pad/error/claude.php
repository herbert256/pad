<?php

  // Machine-readable error output for local tooling: when the request comes from the CLI or
  // from curl on ::1 (padClaudeCheck), padClaudeError answers with a 500 and a JSON body
  // holding the message, file, line, backtrace and every global - bucketed by padClaudeFields
  // into pad (pad*), sequence (pq*), php (_*) and application variables, credentials
  // redacted by padRedact - then exits. $padDiagnostics = FALSE closes the channel.
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

    if ( ( $GLOBALS ['padDiagnostics'] ?? TRUE ) === FALSE )
      return FALSE;

    $agent = $_SERVER ['HTTP_USER_AGENT'] ?? '';

    return padLoopback () and str_contains ( $agent, 'curl' );

  }

  // A request this machine made to itself: the loopback address - both families, since
  // localhost resolves to either and which one a connection takes is a coin toss under
  // load - and no forwarding header. A proxy on the same box connects from loopback for
  // every visitor, so a request that says it was forwarded stands for somebody else. The
  // Host header is the client's to write and decides nothing. Shared by padLocal().

  function padLoopback () {

    $addr = $_SERVER ['REMOTE_ADDR'] ?? '';

    if ( ! in_array ( $addr, [ '127.0.0.1', '::1', '::ffff:127.0.0.1' ], TRUE ) )
      return FALSE;

    foreach ( [ 'HTTP_X_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_X_REAL_IP',
                'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_HOST' ] as $header )
      if ( isset ( $_SERVER [$header] ) )
        return FALSE;

    return TRUE;

  }

  // What a diagnostic shows of a value: credentials, authorization, cookies and the like
  // never leave the process in clear, whoever the report is for. Walks arrays by key; a
  // key that names a secret has its value replaced. $deep also blanks every value under
  // _SESSION and _COOKIE - the reports that go to disk.

  function padRedact ( $value, $key = '', $deep = FALSE ) {

    $key = (string) $key;

    if ( preg_match ( '/pass(word|wd)?$|passwd|secret|token|authorization|auth_pw|api_?key|private_?key|^(http_)?cookie$|^phpsessid$|^padsesid$/i', $key ) )
      return '*** redacted ***';

    if ( ! is_array ( $value ) )
      return $value;

    foreach ( $value as $k => $v )
      if ( ( $key == '_COOKIE' ) or ( $deep and $key == '_SESSION' ) )
        $value [$k] = is_array ( $v ) ? '*** redacted ***' : ( $v === '' ? '' : '*** redacted ***' );
      else
        $value [$k] = padRedact ( $v, $k, $deep );

    return $value;

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

      // Whatever the page had buffered so far would land in front of the JSON and make the
      // body unreadable - padBootStop calls this before its own buffer cleanup.

      while ( ob_get_level () )
        ob_end_clean ();

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

    foreach ($GLOBALS as $key => $value) {

      $value = padRedact ( $value, $key );

      if ( substr($key, 0, 3)  == 'pad' )
        $pad [$key] = $value;
      elseif ( substr($key, 0, 2)  == 'pq' )
        $seq [$key] = $value;
      elseif ( substr($key, 0, 1)  == '_' )
        $php [$key] = $value;
      else
        $app [$key] = $value;

    }

  }


?>