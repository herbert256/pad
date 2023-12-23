<?php


  function padErrorGo ( $error, $file, $line ) {

    set_error_handler ( 'padThrow' );

    try {

      if ( isset ( $GLOBALS ['padErrorGo'] ) )
        throw new Exception ( "$file:$line $error" );

      $GLOBALS ['padErrorGo']   = TRUE;
      $GLOBALS ['padErrorFile'] = $file;
      $GLOBALS ['padErrorLine'] = $line;

      $error = "$file:$line " . padMakeSafe ( $error );

      if ( padInfo and function_exists ( 'padInfoError' ) )
        padInfoError ( $error );

      if ( $GLOBALS ['padErrorLog'] ) 
        error_log ( $error, 4 );

      if ( $GLOBALS ['padErrorReport'] )
        padDumpToDir ( $error );

      padDump ( $error );
   
    } catch (Throwable $e) {

      include pad . 'error/stop.php';

    }
      
    padExit ( TRUE );

  }

?>