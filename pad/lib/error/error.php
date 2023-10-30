<?php
  

  function padErrorReporting ( $level ) {

    $none    = (int) 0;
    $error   = (int) $none    | E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT;

    error_reporting ( $$level );
    
  }


  function padError ($error) {
 
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    $file     = $file     ?? '???';
    $line     = $line     ?? '???';
    $function = $function ?? '???';

    if ( $GLOBALS ['padErrorAction'] == 'php' ) { 
      trigger_error ("$file:$line $error", E_USER_ERROR);
      return FALSE;
    }
 
    if ( $GLOBALS ['padErrorAction'] == 'boot' )
      return padBootStop ( $error, $file, $line ); 

    return padErrorGo ( 'PAD: ' . $error, $file, $line ); 
 
  }


  function padErrorHandler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
      return padErrorGo ( 'ERROR: ' . $error, $file, $line );
 
  }


  function padErrorException ( $error ) {

    $GLOBALS ['padErrorException'] = $error;

    return padErrorGo ( 'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
     
  }


  function padErrorShutdown () {

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL ) 
      return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
  
  }


  function padErrorExit ( $error ) {
    
    padEmptyBuffers ();

    if ( ! headers_sent () )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    if ( padLocal () )
      echo "\n<pre>$error</pre>";
    else
      echo 'Error: ' . padID ();

    $GLOBALS ['padSkipShutdown']     = TRUE;
    $GLOBALS ['padSkipBootShutdown'] = TRUE;

    exit;
  
  }


  function padErrorThrow ( $severity, $message, $filename, $lineno ) {

    throw new ErrorException ( $message, 0, $severity, $filename, $lineno );

  }


  function padThrow ( $message ) {

    throw new Exception ( $message );

  }




?>