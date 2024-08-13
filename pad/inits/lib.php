<?php

  set_error_handler ( 
    function ( $errno, $errstr, $errfile, $errline ) {
      throw new ErrorException ( $errstr, $errno, 0, $errfile, $errline);
    }
  );

  try {

    $padLibDirectory = new RecursiveDirectoryIterator ( pad . '_lib' );
    $padLibIterator  = new RecursiveIteratorIterator  ( $padLibDirectory );

    foreach ( $padLibIterator as $padLibOne ) {

      $padLibFile = str_replace ('\\', '/' , $padLibOne->getPathname() );

      if ( substr($padLibFile, -4) == '.php' )
        include_once $padLibFile;

    }
    
  } catch ( Throwable $e ) {

    echo $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() ;

    exit;

  }

  restore_error_handler ();

?>