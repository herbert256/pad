<?php

  padErrorReporting   ( $padErrorLevel );
  padErrorRestoreBoot ();

  set_error_handler          ( 'padErrorHandler'   );
  set_exception_handler      ( 'padErrorException' );
  register_shutdown_function ( 'padErrorShutdown'  );

  function padErrorReporting ( $level ) {

    $none    = (int) 0;
    $error   = (int) $none    | E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING |
                                E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED ;

    error_reporting ( $$level );

  }

  function padErrorHandler ( $type, $error, $file, $line ) {

    if ( error_reporting() & $type )
      padErrorGo ( 'ERROR: ' . $error, $file, $line );

    return TRUE;

  }

  function padErrorException ( $padException ) {

    $GLOBALS ['padException'] = $padException;

    global $padExceptionFile, $padExceptionLine, $padExceptionError, $padExceptionText;

    $padExceptionFile  = $padException->getFile();
    $padExceptionLine  = $padException->getLine();
    $padExceptionError = $padException->getMessage();
    $padExceptionText  = "$padExceptionFile:$padExceptionLine $padExceptionError" ;

    return padErrorGo (
             'EXCEPTION: ' .
             $padException->getMessage() , $padException->getFile(), $padException->getLine()
    );

  }

  function padErrorShutdown () {

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL )
      return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );

  }

?>
