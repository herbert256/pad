<?php

  // Machine-readable error output for local tooling: when the request comes from the CLI or
  // from curl on ::1 (padClaudeCheck), padClaudeError answers with a 500 and a JSON body
  // holding the message, file, line, the template position (template: file, line, column,
  // tag, excerpt, suggest, wrapped, link), backtrace and every global - bucketed by padClaudeFields
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

  // A request switch that only this machine's own crawls send - padReference (the reference
  // build writes xref files under DATA) and padExamples (the examples harvest; both bypass
  // the page cache and tidy) - counts only on the command line or from loopback: any
  // visitor could flip them before.

  function padSelfSwitch ( $name ) {

    return isset ( $_REQUEST [$name] ) and ( PHP_SAPI === 'cli' or padLoopback () );

  }

  // What a diagnostic shows of a value: credentials, authorization, cookies and the like
  // never leave the process in clear, whoever the report is for. Walks arrays by key; a
  // key that names a secret has its value replaced. $deep also blanks every value under
  // _SESSION and _COOKIE - the reports that go to disk. The application key, $padAppKey,
  // is one: whoever reads it can forge every signed link and open every sealed value.

  function padRedact ( $value, $key = '', $deep = FALSE ) {

    if ( padRedactName ( $key ) )
      return '*** redacted ***';

    if ( is_string ( $value ) )
      return padRedactText ( is_int ( $key ) ? padRedactLine ( $value ) : $value );

    if ( ! is_array ( $value ) )
      return $value;

    foreach ( $value as $k => $v )
      if ( ( $key == '_COOKIE' ) or ( $deep and $key == '_SESSION' ) )
        $value [$k] = is_array ( $v ) ? '*** redacted ***' : ( $v === '' ? '' : '*** redacted ***' );
      else
        $value [$k] = padRedact ( $v, $k, $deep );

    return $value;

  }

  // Whether a name - a key, a variable, a header - is a secret's. A name is read as HTTP
  // writes it as well: X-Api-Key and Set-Cookie went out in clear, the pattern knowing only
  // the underscore, and so did the USERPWD of a fetch with a login and a passphrase.

  function padRedactName ( $name ) {

    return (bool) preg_match ( '/pass(word|wd|phrase)?$|passwd|pwd|secret|token|authorization|auth_pw|api_?key|app_?key|private_?key|^(http_|set_)?cookies?$|^phpsessid$|^padsesid$/i',
                               str_replace ( '-', '_', (string) $name ) );

  }

  // A list holds headers as lines - headers_list (), the PAD headers - so a line is
  // redacted after its name when the name is a secret's: Set-Cookie: carries the session.

  function padRedactLine ( $line ) {

    if ( preg_match ( '/^([A-Za-z0-9_-]+)(\s*:\s*)/', $line, $match ) and padRedactName ( $match [1] ) )
      return $match [1] . $match [2] . '*** redacted ***';

    return $line;

  }

  // A text can be PHP source that assigns a secret: $padConfigApp holds the whole of the
  // application's _config/config.php, where the database password and the application key
  // are written, and it went into every report in clear beside the redacted globals of the
  // same names. The value of each assignment to a secret's name - $name = ..., 'name' =>
  // ..., define ( 'NAME', ... ) - is redacted; the rest of the text stays as it was.

  function padRedactText ( $text ) {

    // The password inside a URL - a DSN in the environment, a fetch with a login in it.

    if ( str_contains ( $text, '@' ) )
      $text = preg_replace ( '~(\b[a-z][a-z0-9+.-]*://[^\s/?#@:]*:)[^\s/?#@]+@~i', '$1*** redacted ***@', $text );

    if ( ! str_contains ( $text, '=' ) and stripos ( $text, 'define' ) === FALSE )
      return $text;

    $value = '(\'(?:[^\'\\\\]|\\\\.)*\'|"(?:[^"\\\\]|\\\\.)*"|[^,;)\]\r\n]*)';

    $text = preg_replace_callback ( '/(\$([A-Za-z_]\w*)((?:\s*\[[^\]\r\n]*\])*)\s*=(?![=>])\s*)' . $value . '/',
      fn ( $m ) => ( padRedactName ( $m [2] ) or padRedactName ( trim ( (string) strrchr ( '[' . $m [3], '[' ), "[]'\" \t" ) ) )
                   ? $m [1] . "'*** redacted ***'" : $m [0],
      $text );

    return preg_replace_callback ( '/((?:define\s*\(\s*)?([\'"])([A-Za-z_][\w.-]*)\2\s*(?:=>|,)\s*)' . $value . '/i',
      fn ( $m ) => ( padRedactName ( $m [3] ) and ( $m [1] [0] != "'" and $m [1] [0] != '"' or str_contains ( $m [1], '=>' ) ) )
                   ? $m [1] . "'*** redacted ***'" : $m [0],
      $text );

  }

  function padClaudeError ( $error, $file, $line ) {

    if ( padClaudeCheck () ) {

      padClaudeFields ( $app, $pad, $php, $seq );

      $claude ['error']    = $error;
      $claude ['file']     = $file;
      $claude ['line']     = $line;

      // Where in the template the error stands - file, line, column, the tag, the lines
      // around it, a near name - next to the engine's PHP file and line (lib/source.php).

      if ( function_exists ( 'padErrorTemplate' ) and ( $template = padErrorTemplate ( (string) $error ) ) )
        $claude ['template'] = $template;

      $claude ['stack']    = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS);
      $claude ['app']      = $app;
      $claude ['pad']      = $pad;
      $claude ['sequence'] = $seq;
      $claude ['php']      = $php;

      // Whatever the page had buffered so far would land in front of the JSON and make the
      // body unreadable - padBootStop calls this before its own buffer cleanup.

      while ( ob_get_level () )
        ob_end_clean ();

      // After an early {flush} (lib/flush.php) the status and the type have gone out with
      // the first part of the page; the report follows it as it can.

      if ( ! headers_sent () ) {
        header ( 'HTTP/1.0 500 Internal Server Error' );
        header ( 'Content-Type: application/json' );
      }

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
