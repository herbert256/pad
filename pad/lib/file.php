<?php


  function padFileContains ( $file, $string ) {

    $file = str_replace ( '.php', '.pad', $file );

    if ( file_exists( $file ) ) 
      if ( str_contains ( file_get_contents ( $file ), $string ) ) 
        return TRUE;

    return FALSE;

  }


  function padIsDir ( $dir ) {

    if ( ! padValidFile ( $dir ) )
      return FALSE;
    else
      return is_dir ( $dir );

  }


  function padFileCorrectX ( $file ) {

    $file = str_replace ( ':', '_', $file );
    $file = str_replace ( '@', '_', $file );
    $file = str_replace ( "'", '_', $file );
    $file = str_replace ( '=', '_', $file );

    return $file;

  }


  function padFileGetContents ( $file ) {

    if ( padTrace and function_exists ('padTrace') )
      include pad . 'info/events/get.php';

    if ( is_dir ($file) )
      return $file;

    if ( ! file_exists($file) )
      return '';

    return file_get_contents ($file);

  }


  function padFilePutContents ($in, $data='', $append=0) {

    if ( padTrace and function_exists ('padTrace') )
      include pad . 'info/events/put.php';

    global $pad;
    
    $file = padData . str_replace(':', '_', $in);

    # $file = padFileCorrect ( $file );

    if ( ! padValidFile ( $file ) )
      return padError ("Invalid file name: $file");

    padFileChkDir  ( $file );
    padFileChkFile ( $file );

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
      
    if ($data)
      if ($append) file_put_contents ($file, "$data\n", LOCK_EX | FILE_APPEND);
      else         file_put_contents ($file, $data,     LOCK_EX);
    
  }


  function padFileChkDir ( $file ) {

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! file_exists($dir) )
      mkdir ($dir, $GLOBALS ['padDirMode'], true );

  }


  function padFileChkFile ( $file ) {

    if ( ! file_exists($file) ) {      
      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);
    }

  }


?>