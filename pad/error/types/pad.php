<?php

  // $padErrorAction 'pad', the default: report the error the PAD way and abort with a 500.
  //
  // padErrorGo wraps padErrorTry so that an error raised while reporting becomes a throwable
  // and is handled by padErrorStop (pad/lib/error.php) instead of looping. padErrorTry stores
  // the message in $padErrorGo / $padErrorFile / $padErrorLine, marks it in the trace via
  // padInfoTraceError, logs it when $padErrorLog, writes a dump directory when $padErrorReport,
  // and finally calls padDump (pad/lib/dump.php) to render the PAD error page.
  //
  // A second error arriving while the first is still being reported is caught by the
  // $padErrorGo guard and diverted to padErrorDouble, which only logs both messages and shows
  // them; a third gives up and goes straight to exits/exit.php.

  include PAD . "error/error.php";

  function padErrorGo ( $error, $file, $line ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorTry ( $error, $file, $line );

    } catch (Throwable $e) {

      padErrorStop ( "$file:$line $error", $e );

    }

    restore_error_handler ();

    padExit ( 500 );

  }

  function padErrorTry ( $error, $file, $line ) {

    global $padErrorFile, $padErrorGo, $padErrorLine, $padErrorLog, $padErrorReport;

    if ( isset ( $padErrorGo ) )
      return padErrorDouble ( $padErrorGo, "$file:$line $error" );
    else
      $padErrorGo = TRUE;

    $go = padMakeSafe ( "$file:$line $error" );

    $padErrorGo   = $go;
    $padErrorFile = $file;
    $padErrorLine = $line;

    if ( function_exists ( 'padInfoTraceError' ) )
      padInfoTraceError ( $go );

    if ( $padErrorLog )
      padLogError ( $go );

    if ( $padErrorReport )
      padDumpToDir ( $go );

    padDump ( $go );

  }

  function padErrorDouble ( $error1, $error2 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorDoubleTry ( $error1, $error2 );

    } catch (Throwable $e) {

      padErrorStop ( "$error1\n$error2", $e );

    }

    restore_error_handler ();

    padExit ( 500 );

  }

  function padErrorDoubleTry ( $error1, $error2 ) {

    global $padErrorDouble;

    if ( isset ( $padErrorDouble ) )
      include PAD . 'exits/exit.php';
    else
      $padErrorDouble = TRUE;

    padErrorLog ( $error1 );
    padErrorLog ( $error2 );

    padErrorExit ( "$error1\n$error2" );

  }

?>