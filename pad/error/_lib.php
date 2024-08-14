<?php
  

  function padErrorHandler ( $type, $error, $file, $line ) {
 
    $GLOBALS ['catch'] .= '-E1';

    if ( error_reporting() & $type )
      return padErrorGo ( 'ERROR: ' . $error, $file, $line );
 
  }


  function padErrorException ( $error ) {

    $GLOBALS ['catch'] .= '-E2';

    $GLOBALS ['padErrorException'] = $error;

    return padErrorGo ( 'EXCEPTION: ' . $error->getMessage() , $error->getFile(), $error->getLine() );
     
  }


  function padErrorShutdown () {

    $GLOBALS ['catch'] .= '-E3';

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL ) 
      return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
  
  }


  function padErrorReporting ( $level ) {

    $none    = (int) 0;
    $error   = (int) $none    | E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING | 
                                E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED | E_STRICT;

    error_reporting ( $$level );
    
  }


?>