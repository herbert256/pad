<?php

  
  function padError ($error) {
  
    return FALSE;

  }


  function padErrorHandler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
     return FALSE;

  }


  function padErrorException ( $error ) {

    return FALSE;

  }


  function padErrorShutdown () {

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL ) 
      return FALSE;

  }
  

?>