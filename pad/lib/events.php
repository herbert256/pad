<?php

  // Application event hooks: a file in an application's _events/ directory runs whenever
  // the engine reaches that moment, on every request - the hooks in pad/events/ serve the
  // info modes and run only under $padInfo, so an application had no way to hear of an
  // error, a query or the finished page without editing the engine.
  //
  //   _events/error.php    an error was raised        $error, $file, $line
  //   _events/sql.php      db() ran a statement       $sql, $input, $vars, $result, $rows, $ms
  //   _events/curl.php     a remote fetch finished    $url, $result, $error, $ms
  //   _events/output.php   the page is about to go    $output - change it to change the page
  //
  // The set is small on purpose: these four are the moments an integration needs - report
  // an error to a tracker, log a slow query or a failing API, post-process the final HTML -
  // and their variables are a promise the engine keeps, where the info hooks are free to
  // follow the engine's internals.
  //
  // The lookup is the one _callbacks and _options use: the page's directory first, then up
  // to the application root, the first file found wins - a subdirectory can hear an event
  // differently, or be the only part of the application that hears it. The answer is kept
  // per directory and event for the rest of the request, so the queries of a busy page do
  // not each walk the directories.
  //
  // A hook runs in a function scope of its own: the event's values are its local variables,
  // and the page's variables are reached with global or $GLOBALS. What it echoes is
  // discarded - a hook speaks through the variables an event lets it change, which today is
  // only output's $output. A hook is not re-entered: a query run inside the sql hook, or an
  // error inside the error hook, does not call the same hook again.

  function padEvent ( $event, $vars ) {

    static $busy = [];

    $file = padEventCheck ( $event );

    if ( ! $file or isset ( $busy [$event] ) )
      return $vars;

    $busy [$event] = TRUE;

    try {

      $vars = padEventRun ( $file, $vars );

    } finally {

      unset ( $busy [$event] );

    }

    return $vars;

  }

  // The error event comes from the error handlers, which must not have their own report
  // overtaken by a second one: a hook that fails is logged and set aside, and the error it
  // was told about is handled as it would have been without it.

  function padEventError ( $error, $file, $line ) {

    global $padErrorTry, $padEventErrorBusy;

    if ( ! padEventCheck ( 'error' ) )
      return;

    // A padError raised inside the hook comes back as a throwable as well (padErrorHook), and
    // the try guards stand aside while it runs: a guard's catch file reports straight to the
    // error action, and an expression of the hook that divided by zero was the error shown.

    $padEventErrorBusy = TRUE;
    $padEventErrorTry  = $padErrorTry;
    $padErrorTry       = FALSE;

    set_error_handler ( 'padErrorThrow' );

    try {

      padEvent ( 'error', [ 'error' => $error, 'file' => $file, 'line' => $line ] );

    } catch ( Throwable $e ) {

      padLogError ( 'the _events/error.php hook failed: ' . padErrorGet ( $e ) );

    } finally {

      restore_error_handler ();

      $padEventErrorBusy = FALSE;
      $padErrorTry       = $padEventErrorTry;

    }

  }

  function padEventCheck ( $event ) {

    global $padDir;

    static $found = [];

    // Before the application is resolved there is no directory chain to look in: an error
    // during boot is the engine's alone.

    if ( ! defined ( 'APP2' ) or ! isset ( $padDir ) )
      return FALSE;

    $key = "$padDir:$event";

    if ( isset ( $found [$key] ) )
      return $found [$key];

    foreach ( padDirs () as $dir )
      if ( is_file ( APP2 . $dir . "_events/$event.php" ) )
        return $found [$key] = APP2 . $dir . "_events/$event.php";

    return $found [$key] = FALSE;

  }

  function padEventRun ( $padEventFile, $padEventVars ) {

    extract ( $padEventVars, EXTR_SKIP );

    ob_start ();

    try {

      include $padEventFile;

    } finally {

      ob_end_clean ();

    }

    foreach ( $padEventVars as $padEventName => $padEventValue )
      $padEventVars [$padEventName] = $$padEventName;

    return $padEventVars;

  }

?>
