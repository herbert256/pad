<?php


  function padExists ( $file ) {

    if ( ! padFileValidName ( $file ) )
      return FALSE;
    else
      return file_exists ( $file );

  }


  function padIsDir ( $dir ) {

    if ( ! padFileValidName ( $dir ) )
      return FALSE;
    else
      return is_dir ( $dir );

  }


  function padFileGetContents ( $file ) {

    if ( ! padFileValidName ( $file ) )
      return padError ("Invalid file name: $file");
    
    if ( ! padExists($file) )
      return '';

    return file_get_contents ($file);

  }


  function padFilePutContents ($in, $data='', $append=0) {

    global $pad;
    
    $file = padData . str_replace(':', '_', $in);

    if ( ! padFileValidName ( $file ) )
      return padError ("Invalid file name: $file");

    padFileChkDir  ( $file );
    padFileChkFile ( $file );

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
      
    if ($data)
      if ($append) file_put_contents ($file, "$data\n", LOCK_EX | FILE_APPEND);
      else         file_put_contents ($file, $data,     LOCK_EX);
    
  }


  function padFileValidName ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_-]+$/', $file) ) return FALSE;
    if ( strpos($file, '//') !== FALSE )                  return FALSE;
    if ( strpos($file, '..') !== FALSE )                  return FALSE;
    if ( strpos($file, '/.') !== FALSE )                  return FALSE;
    if ( strpos($file, './') !== FALSE )                  return FALSE;

    if ( str_starts_with($file, pad)     ) return TRUE;
    if ( str_starts_with($file, padApp)  ) return TRUE;
    if ( str_starts_with($file, padData) ) return TRUE;

    return FALSE;

  }


  function padFileChkDir ( $file ) {

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! padExists($dir) )
      mkdir ($dir, $GLOBALS ['padDirMode'], true);

  }


  function padFileChkFile ( $file ) {

    if ( ! padExists($file) ) {      
      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);
    }

  }


?>