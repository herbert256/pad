<?php
  

  include "error/error.php";
  
  
  function padErrorGo ( $error, $file, $line ) {
 
    set_error_handler ( 'padErrorThrow' );

    try {

      if ( isset ( $GLOBALS ['padErrorGo'] ) )
        throw new Exception ( "$file:$line $error" );

      $GLOBALS ['padErrorGo']   = TRUE;
      $GLOBALS ['padErrorFile'] = $file;
      $GLOBALS ['padErrorLine'] = $line;

      $error = "$file:$line " . padMakeSafe ( $error );

      if ( function_exists ( 'padInfoTraceError' ) )
        padInfoTraceError ( $error );

      if ( $GLOBALS ['padErrorLog'] ) 
        padLogError ( $error );
      
      if ( $GLOBALS ['padErrorReport'] )
        padDumpToDir ( $error );

      $a2 = 2 / 0;

      padDump ( $error );
   
    } catch (Throwable $e) {

      include 'error/stop.php';

    }
      
    padExit ( 500 );

  }


?>