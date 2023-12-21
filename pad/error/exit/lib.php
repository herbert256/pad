<?php

  
  function padError ($error) {
  
    padExit ( TRUE );

  }


  function padErrorHandler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type ) 
      padExit ( TRUE );

  }


  function padErrorException ( $error ) {

    padExit ( TRUE );

  }


  function padErrorShutdown () {

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL ) 
      padExit ( TRUE );

  }
  

?>