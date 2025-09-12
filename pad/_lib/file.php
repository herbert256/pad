<?php


  function padFileGetApp ( $file, $default='' ) {

    return padFileGet ( APP . $file, $default );

  }


  function padFileGetDat ( $file, $default='' ) {

    return padFileGet ( DAT . $file, $default );

  }


  function padFileGetPad ( $file, $default='' ) {

    return padFileGet ( PAD . $file, $default );

  }


  function padFilePutApp ( $file, $data='', $append=0 ) {

    return padFilePut ( APP . $file, $data, $append );

  }


  function padFilePutDat ( $file, $data='', $append=0 ) {

    return padFilePut ( DAT . $file, $data, $append );
 
  }


  function padFilePutPad ( $file, $data='', $append=0 ) {

    return padFilePut ( PAD . $file, $data, $append );
 
  }


  function padFileGet ( $file, $default='' ) {

    if ( $GLOBALS ['padInfo'] )
      include 'events/get.php';

    if ( is_dir ($file) )
      return $file;

    if ( ! file_exists($file) )
      return $default;

    return file_get_contents ($file);

  }


  function padFilePut ( $file, $data='', $append=0 ) {

    if ( $GLOBALS ['padInfo'] )
      include 'events/put.php';

    padFileChkDir  ( $file );
    padFileChkFile ( $file );

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
      
    if ($data)
      if ($append) file_put_contents ($file, "$data\n", LOCK_EX | FILE_APPEND);
      else         file_put_contents ($file, $data,     LOCK_EX);
    
  }


  function padFileContains ( $file, $string ) {

    $file = str_replace ( '.php', '.pad', $file );

    if ( file_exists( $file ) ) 
      if ( str_contains ( file_get_contents ( $file ), $string ) ) 
        return TRUE;

    return FALSE;

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