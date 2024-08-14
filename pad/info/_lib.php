<?php


  function padInfoPadOccur () {

    global $pad, $padOccur;

    $return = $pad;

    $occur = $padOccur [$pad] ?? 0;

    if ( $occur <> 0 and $occur <> 99999 )
      $return .= "/$occur";

    return $return;

  }


  function padInfoLine ( $file, $data, $app=0 ) {

    padInfoWrite ( $file, $data, 1, $app ); 
    
  }


  function padInfoFile ( $file, $data, $app=0 ) {
    
    padInfoWrite ( $file, $data, 0, $app ); 
    
  }


  function padInfoWrite ( $file, $data, $append, $app ) {

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);

    if ( ! $append and file_exists ($file) )
      return;

    if ( $app )
      $file = padApp . $file;
    else
      $file = padData . $file;

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! file_exists ($dir) )
      padInfoMkDir ( $dir );

    if ( ! file_exists ($file) ) {      
      touch ($file);
      chmod ($file, $GLOBALS ['padFileMode']);
    }

    if ( $append )
      file_put_contents ( $file, $data . "\n", LOCK_EX | FILE_APPEND );
    else
      file_put_contents ( $file, $data,        LOCK_EX );
    
  }


  function padInfoMkDir( $dir ) {

   set_error_handler ( 'padErrorThrow' );

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


  function padInfoDelete ( $dir ) {

    if ( ! file_exists ( $dir ) )
      return;

    $loop = opendir ( $dir );

    while ( ( $file = readdir ( $loop ) ) !== FALSE )

      if ( $file <> '.' and $file <> '..' )
        if ( is_dir ( "$dir/$file" ) )
          padInfoDelete ( "$dir/$file" );
        else
          unlink ( "$dir/$file" ) ;
        
    closedir ( $loop );

    rmdir ( $dir );

  }


?>