<?php

  
  function padError ($error) {
   
    padStop ( 500 );

  }


  function padErrorHandler ( $type, $error, $file, $line ) {
 
    if ( error_reporting() & $type )
      padStop ( 500 );

  }


  function padErrorException ( $error ) {

      padStop ( 500 );

  }


  function padErrorShutdown () {

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL ) 
      padStop ( 500 );

  }
  

?>