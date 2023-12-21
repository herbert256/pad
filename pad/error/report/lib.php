<?php

  
  function padError ($error) {
   
    error_log ( $error, 4 );
 
  }


  function padErrorHandler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
      error_log ( "$file:$line $error", 4 );
 
  }


  function padErrorException ( $error ) {

      error_log ( $error->getFile() . ':' . $error->getLine() . ' ' . $error->getMessage(), 4 );

  }


  function padErrorShutdown () {
 
    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL ) 
      error_log ( $error['file'] . ':' . $error['line'] . ' ' . $error['message'], 4 );

  }
  

?>