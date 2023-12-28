<?php


  function padInfoPadOccur () {

    global $pad, $padOccur;

    $return = $pad;

    $occur = $padOccur [$pad] ?? 0;

    if ( $occur <> 0 and $occur <> 99999 )
      $return .= "/$occur";

    return $return;

  }


  function padInfoLine ( $file, $data ) {

    padInfoWrite ( $file, $data, 1 ); 
    
  }


  function padInfoFile ( $file, $data ) {
    
    padInfoWrite ( $file, $data, 0 ); 
    
  }


  function padInfoWrite ( $file, $data, $append=0 ) {

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);

    $file = padFileCorrect($file);

    padInfoCheck ( $file );

    if ( $append )
      file_put_contents ( padData . $file, $data . "\n", LOCK_EX | FILE_APPEND );
    else
      file_put_contents ( padData . $file, $data,        LOCK_EX );
    
  }


  function padInfoCheck ( $file ) {

    $file = padData . $file;

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! file_exists ($dir) )
      padInfoMkDir ( $dir );

    if ( ! file_exists ($file) ) {      
      touch ($file);
      chmod ($file, $GLOBALS ['padFileMode']);
    }
    
  }


  function padInfoMkDir( $dir ) {

   set_error_handler ( 'padThrow' );

    try {

      mkdir ($dir, $GLOBALS ['padDirMode'], true );

    } catch (Throwable $e) {

  
    }

    restore_error_handler ();  
    
  }


  function padInfoGet ( $file ) {

    if ( ! file_exists ($file) )
      return '';

    return file_get_contents ($file);

  }


?>