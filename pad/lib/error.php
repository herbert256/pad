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


  function padErrorGo ($error, $file, $line) {

    if ( $GLOBALS['padExit'] <> 1 )
      padErrorStop ( "ERROR-SECOND: $error", $file, $line);

    $GLOBALS['padExit'] = 2;
    
    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    $reporting = error_reporting (0);

    try {
 
      $error = "$file:$line " . padMakeSafe ( $error );

      $GLOBALS ['padErrrorList'] [] = $error; 

      $GLOBALS['padErrorFile']  = $file;
      $GLOBALS['padErrorLine']  = $line;

      if ( $GLOBALS ['padErrorLog'] or $GLOBALS ['padErrorAction'] == 'report' )
        padErrorLog ( $error );

      if ( $GLOBALS ['padErrorReport'] or $GLOBALS ['padErrorAction'] == 'report' )
        padDumpToDir ( $error );

      if ( $GLOBALS ['padErrorAction'] == 'exit') {
        padHeader ('HTTP/1.0 500 Internal Server Error' );
        padExit ();
      }

      if ( $GLOBALS ['padErrorAction'] == 'stop' )
        padStop ( 500 );

      if ( $GLOBALS ['padErrorAction'] == 'pad' )
        padDump ( $error );

    } catch (Throwable $e) {

      padErrorStop ( 'ERROR-CATCH: ' . $e->getMessage(), $e->getFile(), $e->getLine() );

    }

    error_reporting ($reporting);
    restore_error_handler ();

    $GLOBALS ['padExit'] = 1;
      
    return FALSE;

  }



  function padErrorCheck ( $type, $info ) {

    $md5 = md5 ( trim($info) );

    if ( isset ( $GLOBALS["padErrorCheck_$type"] ) and isset ( $GLOBALS["padErrorCheck_$type"] [$md5] ) )
      return TRUE;

    $GLOBALS["padErrorCheck_$type"] [$md5] = TRUE;

    return FALSE;

  }


  function padErrorLog ( $info ) {

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    $reporting = error_reporting (0);

    try {

      padErrorLogGo ( $info );
    
    } catch (Throwable $e) {
    
      padErrorLogCatch ( $info, $e );
    
    }

    error_reporting ($reporting);
    restore_error_handler ();

  }


  function padErrorLogGo ( $info ) {

    $log = '[PAD] ' . padID () . ' - ' . padMakeSafe ( $info );

    if ( ! padErrorCheck ( 'log', $log )  )
      error_log ( $log, 4 );

  }


  function padErrorLogCatch ( $info, $e ) {

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    $reporting = error_reporting (0);

    try {

      padFilePutContents ( 'error_log.txt', padID () . ' - ' . $info,  true );

      $log = $e->getFile() . ':' .  $e->getLine() . ' ERROR-LOG: ' . $e->getMessage();

      if ( ! padErrorCheck ( 'catch', $log ) ) 
        padFilePutContents ( 'error_log.txt', padID () . ' - ' . $log, true );
    
    } catch (Throwable $e) {
    
      // giving up
    
    }

    error_reporting ($reporting);
    restore_error_handler ();


  }


  function padErrorStop ( $error, $file, $line) {

    set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
    error_reporting (0);

    try {

      padErrorStopGo ( $error, $file, $line);
    
    } catch (Throwable $e) {
    
      padErrorStopCatch ( $error, $file, $line, $e );
    
    }

    $GLOBALS ['padSkipShutdown']     = TRUE;
    $GLOBALS ['padSkipBootShutdown'] = TRUE;

    exit;

  }


  function padErrorStopGo ( $error, $file, $line ) {

    if ( $GLOBALS ['padErrorLog'] )
      padErrorLog ( "$file:$line $error" );

    if ( isset ( $GLOBALS ['padErrrorList'] ) )
      foreach ( $GLOBALS ['padErrrorList'] as $list )
        $error .= "\n" . $list;

    padBootStop ( $error, $file, $line );

  }


  function padErrorStopCatch ( $error, $file, $line, $e ) {

    $id = $GLOBALS ['padReqID'] ?? uniqid (TRUE);

    if ( isset ( $GLOBALS ['padErrrorList'] ) )
      foreach ( $GLOBALS ['padErrrorList'] as $list )
        padErrorLog ( $list );

    padErrorLog ( "$file:$line $error");
    padErrorLog ( $e->getFile() . ':' .  $e->getLine() . ' ERROR-STOP: ' . $e->getMessage() );

    echo "Error: $id";

  }

 
?>