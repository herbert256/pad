<?php

  // The health check: with $padHealth on, ?up answers whether the application can serve -
  // for a load balancer, a container's health probe or an uptime monitor - when the
  // application has no page of that name.
  //
  //   200  {"status":"ok","checks":{"data":"ok","database":"ok"}}
  //   503  {"status":"fail","checks":{"data":"ok","database":"fail"}}
  //
  // The checks: data - DATA/ is there and writable, as logs, sessions and caches need;
  // database - the application's database answers a SELECT 1, when it has one
  // ($padSqlDatabase set); and whatever the application's own _health.php returns, an
  // array of name => TRUE (ok), FALSE (fail) or a text saying what is wrong (a failure
  // too). A failing database is "fail" and nothing more - the reason, which can name the
  // user and the host, goes to the log. Any failure makes the answer a 503, which a load
  // balancer reads as take this one out. The answer is JSON, never cached, and comes before
  // the page cache and before any of the application runs - _health.php runs in a scope of
  // its own, with the engine's functions but not the application's _lib, as it answers for
  // the machine and not for a page. An application that is down for maintenance answers
  // ?up 503 as every page (lib/maintenance.php).
  //
  // padHealthChecks    the checks, name => 'ok' or 'fail' or the text of the failure
  // padHealthDatabase  whether the application's database answers
  // padHealthHook      the application's _health.php, its answer checked
  // padHealthAnswer    the JSON answer, and the end of the request

  function padHealthChecks () {

    global $padSqlDatabase;

    $checks = [];

    $checks ['data'] = ( is_dir ( DATA ) and is_writable ( DATA ) ) ? 'ok' : 'fail';

    if ( trim ( (string) ( $padSqlDatabase ?? '' ) ) !== '' )
      $checks ['database'] = padHealthDatabase () ? 'ok' : 'fail';

    if ( is_file ( APP . '_health.php' ) )
      foreach ( padHealthHook ( APP . '_health.php' ) as $name => $value )
        $checks [$name] = $value;

    return $checks;

  }

  // A probe that fails comes back as a throwable - padError among them (padErrorHook) - and
  // is logged; the try guards stand aside while it runs.

  function padHealthProbe ( $probe ) {

    global $padErrorTry, $padHealthBusy;

    $tryWas        = $padErrorTry ?? FALSE;
    $padErrorTry   = FALSE;
    $padHealthBusy = TRUE;
    $level         = ob_get_level ();

    set_error_handler ( 'padErrorThrow' );

    ob_start ();

    try {

      return [ TRUE, $probe () ];

    } catch ( Throwable $e ) {

      padLogError ( 'the health check failed: ' . padErrorGet ( $e ) );

      return [ FALSE, NULL ];

    } finally {

      while ( ob_get_level () > $level )
        ob_end_clean ();

      restore_error_handler ();

      $padErrorTry   = $tryWas;
      $padHealthBusy = FALSE;

    }

  }

  function padHealthDatabase () {

    list ( $ran, $ok ) = padHealthProbe ( function () {

      $connect = padDbApp ();

      if ( $connect instanceof PDO )
        return $connect->query ( 'SELECT 1' ) !== FALSE;

      if ( $connect instanceof mysqli )
        return mysqli_query ( $connect, 'SELECT 1' ) !== FALSE;

      return FALSE;

    } );

    return $ran and $ok;

  }

  function padHealthHook ( $padHealthFile ) {

    list ( $ran, $answer ) = padHealthProbe ( function () use ( $padHealthFile ) {
      return include $padHealthFile;
    } );

    if ( ! $ran )
      return [ '_health.php' => 'fail' ];

    if ( ! is_array ( $answer ) ) {
      padLogError ( 'the health check: _health.php must return an array of name => TRUE, FALSE or a text, not ' . get_debug_type ( $answer ) );
      return [ '_health.php' => 'fail' ];
    }

    $checks = [];

    foreach ( $answer as $name => $value ) {

      if ( ! is_string ( $name ) or ! preg_match ( '/^[A-Za-z0-9][A-Za-z0-9_.-]{0,63}$/D', $name ) ) {
        padLogError ( 'the health check: _health.php names a check ' . padMakeSafe ( (string) $name, 40 ) . ' - letters, digits, _ . and -' );
        $checks ['_health.php'] = 'fail';
        continue;
      }

      if     ( $value === TRUE      ) $checks [$name] = 'ok';
      elseif ( is_string ( $value ) ) $checks [$name] = ( trim ( $value ) === '' or $value === 'fail' ) ? 'fail' : padMakeSafe ( $value, 200 );
      else                            $checks [$name] = 'fail';

    }

    return $checks;

  }

  function padHealthAnswer () {

    $checks = padHealthChecks ();
    $ok     = ! array_filter ( $checks, fn ( $value ) => $value !== 'ok' );
    $stop   = $ok ? 200 : 503;
    $body   = json_encode ( [ 'status' => $ok ? 'ok' : 'fail', 'checks' => (object) $checks ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

    padEmptyBuffers ( $ignored );

    if ( ! headers_sent () ) {
      http_response_code ( $stop );
      header ( 'Content-Type: application/json; charset=UTF-8' );
      header ( 'Cache-Control: no-cache, no-store' );
      header ( 'Content-Length: ' . strlen ( $body ) );
      padSecurityHeaders ();
    }

    echo $body;

    if ( session_status () === PHP_SESSION_ACTIVE )
      session_write_close ();

    include PAD . 'exits/exit.php';

  }

?>
