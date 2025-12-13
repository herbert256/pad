<?php
  

  include PAD . "error/error.php";
  
  
  function padErrorGo ( $error, $file, $line ) {
 
    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorTry ( $error, $file, $line );
   
    } catch (Throwable $e) {

      padErrorStop ( "$file:$line $error", $e );

    }
  
    restore_error_handler ();

    padExit ( 500 );

  }


  function padErrorTry ( $error, $file, $line ) {
 
    if ( isset ( $GLOBALS ['padErrorGo'] ) )
      return padErrorDouble ( $GLOBALS ['padErrorGo'], "$file:$line $error" );
    else
      $GLOBALS ['padErrorGo'] = TRUE;

    $go = padMakeSafe ( "$file:$line $error" );

    $GLOBALS ['padErrorGo']   = $go;
    $GLOBALS ['padErrorFile'] = $file;
    $GLOBALS ['padErrorLine'] = $line;

    if ( function_exists ( 'padInfoTraceError' ) )
      padInfoTraceError ( $go );

    if ( $GLOBALS ['padErrorLog'] ) 
      padLogError ( $go );
    
    if ( $GLOBALS ['padErrorReport'] )
      padDumpToDir ( $go );

    padDump ( $go );
 
  }


 function padErrorDouble ( $error1, $error2 ) {
 
    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorDoubleTry ( $error1, $error2 );     
   
    } catch (Throwable $e) {

      padErrorStop ( "$error1\n$error2", $e );

    }
      
    restore_error_handler ();

    padExit ( 500 );

  } 


  function padErrorDoubleTry ( $error1, $error2 ) {
 
    if ( isset ( $GLOBALS ['padErrorDouble'] ) )
      include PAD . 'exits/exit.php';
    else
      $GLOBALS ['padErrorDouble'] = TRUE;

    padErrorLog ( $error1 );
    padErrorLog ( $error2 );

    padErrorExit ( "$error1\n$error2" );     
 
  }


?>