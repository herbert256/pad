<?php

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
