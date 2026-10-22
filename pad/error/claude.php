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

    if ( padDiagnosticsOff () )
      return FALSE;

    $agent = $_SERVER ['HTTP_USER_AGENT'] ?? '';

    return padLoopback () and str_contains ( $agent, 'curl' );

  }

  // A request this machine made to itself: the loopback address - both families, since
  // localhost resolves to either and which one a connection takes is a coin toss under
  // load - and no forwarding header. A proxy on the same box connects from loopback for
  // every visitor, so a request that says it was forwarded stands for somebody else. The
  // Host header is the client's to write and decides nothing. Shared by padLocal().
  //
  // Any header a proxy adds says so, not the client address alone: an nginx that passes on
  // only X-Forwarded-Proto, a Via line, a CDN's or a tunnel's client address header.

  function padLoopback () {

    $addr = $_SERVER ['REMOTE_ADDR'] ?? '';

    if ( ! in_array ( $addr, [ '127.0.0.1', '::1', '::ffff:127.0.0.1' ], TRUE ) )
      return FALSE;

    foreach ( [ 'HTTP_X_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_X_REAL_IP',
                'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_HOST', 'HTTP_X_FORWARDED_PROTO',
                'HTTP_X_FORWARDED_PORT', 'HTTP_X_FORWARDED_SERVER', 'HTTP_VIA',
                'HTTP_FORWARDED_FOR', 'HTTP_X_ORIGINAL_FORWARDED_FOR', 'HTTP_X_CLIENT_IP',
                'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_CF_CONNECTING_IP', 'HTTP_TRUE_CLIENT_IP',
                'HTTP_FASTLY_CLIENT_IP', 'HTTP_X_PAD_REMOTE' ] as $header )
      if ( isset ( $_SERVER [$header] ) )
        return FALSE;

    return TRUE;

  }

  // The engine's own fetches of this site - {get}, {curl 'SELF://...'}, {page} over HTTP -
  // arrive from loopback with nothing forwarded, and were taken for this machine's own even
  // when a visitor from elsewhere had caused them: the page fetched for that visitor showed
  // its {debug} boxes and its toolbar, answered its errors with the full report - or, the
  // visitor's user agent riding along, with the JSON channel - and honoured the loopback
  // switches. Every fetch made for a request that is not local says so, with a header that
  // tells nothing about the visitor, and padLoopback takes a request carrying it for a
  // forwarded one. Whatever the address: deciding by it missed 0.0.0.0, ::ffff:7f00:1, a
  // name resolving to ::1 alone and a site that redirects here, and cost a DNS lookup.
  //
  // A page pad export renders is this machine's here, though padLocalView keeps the
  // developer's helpers out of its copy: what the page fetches of its own site at build time
  // is the copy's content. Sent as a visitor's, the manual's template_errors - whose example
  // fetches the JSON error report only this machine gets - failed the export. Such a fetch
  // says X-PAD-Export, which padLoopback lets pass and padLocalView reads as no screen of
  // this machine's: the page fetched kept its {debug} boxes, and the toolbar, live reload
  // and the output check its PHP switched on, and the copy carried them.

  function padSelfFetchHeaders () {

    if ( ! padLocal () )
      return [ 'X-PAD-Remote' => '1' ];

    return padLocalView () ? [] : [ 'X-PAD-Export' => '1' ];

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

  function padRedact ( $value, $key = '', $deep = FALSE, $depth = 0 ) {

    static $budget = 0, $values = [];

    // The values to redact are gathered once per walk, not for every text in it.

    if ( $depth == 0 )
      $values = padRedactValues ();

    if ( padRedactName ( $key ) )
      return '*** redacted ***';

    if ( is_string ( $value ) )
      return padRedactText ( is_int ( $key ) ? padRedactLine ( $value ) : $value, $values );

    if ( ! is_array ( $value ) )
      return $value;

    // A global array can hold itself - $loop ['self'] = &$loop - and the walk went round it
    // until the call stack ran out, taking the report of the real error with it. Deep enough
    // for any data, and a bound on the arrays one walk visits for a loop that branches.

    if ( $depth == 0 )
      $budget = 100000;

    if ( $depth >= 32 or --$budget < 0 )
      return '*** nested too deep ***';

    // Into a new array: the one walked is a copy, but a row an application walked with
    // foreach ( ... as &$row ) is a reference in it, and the redaction written into the copy
    // went through to the application's own data - after {debug}, after an error under the
    // dump action, the page went on with its passwords replaced.

    $redacted = [];

    foreach ( $value as $k => $v )
      if ( ( $key == '_COOKIE' ) or ( $deep and $key == '_SESSION' ) )
        $redacted [$k] = is_array ( $v ) ? '*** redacted ***' : ( $v === '' ? '' : '*** redacted ***' );
      else
        $redacted [$k] = padRedact ( $v, $k, $deep, $depth + 1 );

    return $redacted;

  }

  // Whether a name - a key, a variable, a header - is a secret's. A name is read as HTTP
  // writes it as well: X-Api-Key and Set-Cookie went out in clear, the pattern knowing only
  // the underscore, and so did the USERPWD of a fetch with a login and a passphrase. The
  // password words count anywhere in the name: a hash kept as password_hash, passwordHash
  // or pass_hash - the column of every users table - showed in clear in {debug}, the
  // toolbar and the reports, the word counting only at the end.

  function padRedactName ( $name ) {

    static $memo = [];

    $name = is_scalar ( $name ) ? (string) $name : '';

    return $memo [$name] ??= (bool) preg_match ( '/pass(word|wd|phrase|code)|pass(_?hash|$)|pwd|secret|token|authorization|auth_pw|api_?key|app_?key|private_?key|^(http_|set_)?cookies?$|^phpsessid$|^padsesid$/i',
                                                 str_replace ( '-', '_', $name ) );

  }

  // A list holds headers as lines - headers_list (), the PAD headers - so a line is
  // redacted after its name when the name is a secret's: Set-Cookie: carries the session.

  function padRedactLine ( $line ) {

    if ( preg_match ( '/^([A-Za-z0-9_-]+)(\s*:\s*)/', $line, $match ) and padRedactName ( $match [1] ) )
      return $match [1] . $match [2] . '*** redacted ***';

    return $line;

  }

  // A query - a form body, the query of a URL in a text - with the value of each field
  // whose name is a secret's redacted, read pair by pair as it stands. No parse_str, which
  // stops at max_input_vars with a warning. A secret's value is redacted whole: in a body up
  // to the next & as parse_str splits it, in a text up to the &, space, quote or # that ends
  // a URL's value there - it ended at a ; or a space, and password=se;cret kept ;cret. Any
  // other value in a text ends at a ; as well: run on to the &, it swallowed the secret
  // after it - a=1;token=abc, a PDO DSN's ...;user=app;password=hunter2 - in clear. Walked
  // pair by pair, each value's end decided by its own name.

  function padRedactQuery ( $query, $body = TRUE ) {

    $name = $body ? '/(?:^|&)([^=&]++)=/' : '/(?:^|[?&;])([^=&#?;\s"\'<>]++)=/';
    $ends = $body ? '&' : "&\"'<># \t\r\n\f\v";
    $out  = '';
    $pos  = 0;

    while ( preg_match ( $name, $query, $match, PREG_OFFSET_CAPTURE, $pos ) ) {

      $at     = $match [0] [1] + strlen ( $match [0] [0] );
      $secret = padRedactQueryName ( $match [1] [0] );
      $end    = $at + strcspn ( $query, ( $secret or $body ) ? $ends : "$ends;", $at );

      $out .= substr ( $query, $pos, $at - $pos ) . ( $secret ? urlencode ( '*** redacted ***' ) : substr ( $query, $at, $end - $at ) );
      $pos  = $end;

    }

    return $out . substr ( $query, $pos );

  }

  // Every key of a bracketed name counts: password[] and user[password][0] are a password's,
  // where only the last key was read.

  function padRedactQueryName ( $field ) {

    foreach ( preg_split ( '/[\[\]]++/', urldecode ( $field ), -1, PREG_SPLIT_NO_EMPTY ) as $part )
      if ( padRedactName ( $part ) )
        return TRUE;

    return FALSE;

  }

  // What a text shows of the request's secrets. The application's config source, where
  // the database password and the application key are written, was the one text that
  // assigned them; inits/config.php keeps it in no global now, and the patterns that read
  // the assignments in every text ran PCRE out of its stack on a long quoted literal - the
  // text came back empty, {debug} and the toolbar answered 500 - and cost a page full of
  // strings five times its time under the toolbar. What stays are cheap, possessive passes,
  // with the values to look for handed in by a walk that gathered them once.

  function padRedactText ( $text, $values = NULL ) {

    // The request's own secrets by their value: the CSRF token stands in every level of a
    // rendered form and in whatever variable a page keeps it in, the session ids in the PAD
    // header line and the cookie lines - under names no pattern knows.

    $values ??= padRedactValues ();

    if ( $values )
      $text = str_replace ( $values, '*** redacted ***', $text );

    // The password inside a URL - a DSN in the environment, a fetch with a login in it.

    if ( str_contains ( $text, '://' ) and str_contains ( $text, '@' ) )
      $text = preg_replace ( '~(\b[a-z][a-z0-9+.-]*+://[^\s/?#@:]*+:)[^\s/?#@]++@~i', '$1*** redacted ***@', $text ) ?? $text;

    // A secret in a query: ?reset&token=... was redacted in $_GET and stood in clear in
    // REQUEST_URI, QUERY_STRING and the referer of the next page.

    if ( str_contains ( $text, '=' ) )
      $text = padRedactQuery ( $text, FALSE );

    return $text;

  }

  // Random values only, long enough not to stand for anything else by chance - and minted by
  // the server: the session cookies are the visitor's to send, and a PHPSESSID or padSesID
  // chosen to read like an attack's payload made that payload disappear from every report
  // of it. PHP's session id counts under strict mode alone, where PHP refuses an id it did
  // not make; PAD's when it is not the one the browser brought.

  function padRedactValues () {

    global $padSesID;

    $values = [ $GLOBALS ['padCsrfIssued'] ?? '', $_SESSION ['padCsrf'] ?? '', $GLOBALS ['padAppKey'] ?? '' ];

    if ( session_status () === PHP_SESSION_ACTIVE and ini_get ( 'session.use_strict_mode' ) )
      $values [] = session_id ();

    if ( isset ( $padSesID ) and ! in_array ( $padSesID, [ $_COOKIE ['padSesID'] ?? NULL, $_REQUEST ['padSesID'] ?? NULL ], TRUE ) )
      $values [] = $padSesID;

    return array_values ( array_filter ( $values, fn ( $value ) => is_string ( $value ) and strlen ( $value ) >= 8 ) );

  }

  function padClaudeError ( $error, $file, $line ) {

    if ( padClaudeCheck () and ! padAnswerSent () ) {

      padClaudeFields ( $app, $pad, $php, $seq );

      // The message too, as every value of the report: a failed {curl} quotes its address,
      // and a login in it was the one place the report had it in clear.

      $claude ['error']    = padRedactText ( (string) $error );
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

  // Every global, of a bounded size: a global can hold a whole page, the list of every value
  // the request spliced, the memory kept in reserve for the boot net - a page that died on
  // PHP's time limit answered a body of 31 MB, and the suite overview that showed it ran out
  // of its gigabyte. Each global has 64 KB, the report 4 MB, and the application's own
  // variables come first, so what the page set is there whole and the engine's take what is
  // left. Bounded before the redaction walks it: that walk itself ran out of memory on the
  // spliced values.

  function padClaudeFields ( &$app, &$pad, &$php, &$seq ) {

    $seq = $php = $pad = $app = [];

    $room   = 4 << 20;
    $values = padRedactValues ();

    foreach ( [ FALSE, TRUE ] as $engine )

      foreach ( $GLOBALS as $key => $value ) {

        if ( $engine !== ( str_starts_with ( $key, 'pad' ) or str_starts_with ( $key, 'pq' ) or str_starts_with ( $key, '_' ) ) )
          continue;

        $left  = min ( 65536, max ( 0, $room ) );
        $own   = $left;
        $value = padRedact ( padClaudeBound ( $value, 0, $left, $values ), $key );
        $room -= $own - $left;

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

  // A value as the report shows it, within $room bytes: a text of more than 4 KB is cut, an
  // array shows 100 entries and 6 levels, an object its public properties and its class, and
  // once the room is spent what follows says only what it was. A text is redacted before it
  // is cut - cut first, a secret's value standing across the cut went past the redaction
  // with its first half.

  function padClaudeBound ( $value, $depth, &$room, $values ) {

    if ( is_string ( $value ) ) {

      $length = strlen ( $value );

      if ( $length <= 4096 and $room >= $length ) {
        $room -= $length;
        return $value;
      }

      $keep  = max ( 0, min ( 4096, $room ) );
      $room -= $keep;

      return substr ( padRedactText ( substr ( $value, 0, $keep + 512 ), $values ), 0, $keep ) . " *** cut - $length bytes ***";

    }

    if ( is_object ( $value ) )
      $value = [ '*class*' => get_class ( $value ) ] + get_object_vars ( $value );

    if ( ! is_array ( $value ) ) {
      $room -= 16;
      return $value;
    }

    $count = count ( $value );

    if ( $depth >= 6 or $room <= 0 )
      return "*** array of $count ***";

    $bound = [];
    $shown = 0;

    foreach ( $value as $key => $item ) {

      if ( $shown == 100 or $room <= 0 ) {
        $bound ['*more*'] = '*** ' . ( $count - $shown ) . ' more ***';
        break;
      }

      $room -= strlen ( (string) $key ) + 8;
      $bound [$key] = padClaudeBound ( $item, $depth + 1, $room, $values );
      $shown++;

    }

    return $bound;

  }


?>
