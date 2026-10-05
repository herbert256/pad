<?php

  // An application's own log: one line per call in a file per day, the levels and the
  // {placeholder} rule of PSR-3 - what Laravel's Log::info() and Monolog give, without a
  // logger object to set up first.
  //
  // padLog         appends one line to DATA/logs/<application>/<Y-m-d>.log -
  //                2026-10-05 12:00:00 INFO Order 42 paid {"order":42} - and answers TRUE
  //
  // padLogLevels   the eight PSR-3 levels, debug to emergency
  // padLogText     a message or a context value as the text that goes into the line
  //
  // The engine's own errors go to PHP's error_log (padLogError); this file is for what the
  // application wants to remember - a payment, a login, a failed import. The time stamp
  // and the file's date are now as padNow answers it, in the application's timezone, so a
  // test that froze the clock (padNowFreeze) finds the same line on every run. A log line
  // never stops the page: a disk that refuses the write is answered FALSE, never an
  // exception. A replayed request (lib/replay.php) writes nothing, like every write of the
  // application's own, and answers TRUE.

  function padLogLevels () {

    return [ 'debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency' ];

  }

  // The message is text; anything else is written as JSON. {name} in it is filled from the
  // context when the context has that name (PSR-3: letters, digits, _ and .), and the whole
  // context follows as JSON - a Throwable in it as its class, message, file and line, a
  // moment as its date and time. CR and LF are written \r and \n, so one call is one line
  // however the message was made.

  function padLog ( $message, $level = 'info', $context = [] ) {

    global $padApp, $padDirMode, $padFileMode;

    $name = is_string ( $level ) ? strtolower ( trim ( $level ) ) : '';

    if ( ! in_array ( $name, padLogLevels (), TRUE ) ) {
      padError ( "padLog: there is no log level named '" . padMakeSafe ( is_scalar ( $level ) ? (string) $level : get_debug_type ( $level ), 40 )
                 . "' - the levels are " . implode ( ', ', padLogLevels () ) );
      return FALSE;
    }

    if ( $context === NULL )
      $context = [];

    if ( ! is_array ( $context ) ) {
      padError ( 'padLog: the context must be an array, not ' . get_debug_type ( $context ) );
      return FALSE;
    }

    $text = padLogText ( $message );

    if ( $context )
      $text = preg_replace_callback ( '/\{([A-Za-z0-9_.]+)\}/',
                fn ( $match ) => array_key_exists ( $match [1], $context ) ? padLogText ( $context [$match [1]] ) : $match [0],
                $text );

    foreach ( $context as $key => $value )
      if ( $value instanceof Throwable )
        $context [$key] = [ 'class'   => get_class ( $value ), 'message' => $value->getMessage (),
                            'file'    => $value->getFile (),   'line'    => $value->getLine ()     ];
      elseif ( $value instanceof DateTimeInterface )
        $context [$key] = $value->format ( 'Y-m-d H:i:s' );

    $text = str_replace ( [ "\r", "\n" ], [ '\r', '\n' ], $text );
    $now  = padNow ();
    $line = $now->format ( 'Y-m-d H:i:s' ) . ' ' . strtoupper ( $name ) . ' ' . $text;

    if ( $context )
      $line .= ' ' . json_encode ( $context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                                           | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR );

    if ( padReplaying () )
      return TRUE;

    $dir  = DATA . 'logs/' . $padApp;
    $file = $dir . '/' . $now->format ( 'Y-m-d' ) . '.log';

    try {

      if ( ! is_dir ( $dir ) and ! @mkdir ( $dir, $padDirMode ?? 0755, TRUE ) and ! is_dir ( $dir ) )
        return FALSE;

      $new  = ! file_exists ( $file );
      $done = @file_put_contents ( $file, "$line\n", FILE_APPEND | LOCK_EX );

      if ( $new and $done !== FALSE )
        @chmod ( $file, $padFileMode ?? 0644 );

      return ( $done !== FALSE );

    } catch ( Throwable $e ) {

      return FALSE;

    }

  }

  // Text as it is; NULL as nothing, a boolean as true or false, a Throwable as its class
  // and message - not the whole trace its text would be - a moment as its date and time,
  // and an array or an object that is no text as JSON.

  function padLogText ( $value ) {

    if ( is_string ( $value ) )
      return $value;

    if ( $value === NULL )
      return '';

    if ( is_bool ( $value ) )
      return $value ? 'true' : 'false';

    if ( $value instanceof Throwable )
      return get_class ( $value ) . ': ' . $value->getMessage ();

    if ( is_int ( $value ) or is_float ( $value ) or $value instanceof Stringable )
      return (string) $value;

    if ( $value instanceof DateTimeInterface )
      return $value->format ( 'Y-m-d H:i:s' );

    $json = json_encode ( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                                | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PARTIAL_OUTPUT_ON_ERROR );

    return ( $json === FALSE ) ? get_debug_type ( $value ) : $json;

  }

?>
