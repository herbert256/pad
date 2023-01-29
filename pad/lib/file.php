<?php


  function padFileGetContents ( $file ) {

    if ( ! padFileValidName ( $file ) )
      return padError ("Invalid file name: $file");
    
    if ( ! file_exists($file) )
      return '';

    padTimingStart ('read');
    $return = file_get_contents ($file);
    padTimingEnd ('read');

    return $return;    

  }


  function padFilePutContents ($in, $data='', $append=0) {

    global $pad;
    
    $file = DATA . $in;

    if ( ! padFileValidName ( $file ) ) {
      #padDum$GLOBALS ['pad'];
      return padError ("Invalid file name: $file");
    }

    padFileChkDir  ( $file );
    padFileChkFile ( $file );

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
      
    if ($data) {
      padTimingStart ('write');
      if ($append) file_put_contents ($file, "$data\n", LOCK_EX | FILE_APPEND);
      else         file_put_contents ($file, $data, LOCK_EX);
      padTimingEnd ('write');
    }
    
  }


  function padFileValidName ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_-]+$/', $file) ) return FALSE;
    if ( strpos($file, '//') !== FALSE )                  return FALSE;
    if ( strpos($file, '..') !== FALSE )                  return FALSE;
    if ( strpos($file, '/.') !== FALSE )                  return FALSE;
    if ( strpos($file, './') !== FALSE )                  return FALSE;

    if ( str_starts_with($file, PAD)  ) return TRUE;
    if ( str_starts_with($file, DATA) ) return TRUE;

    return FALSE;

  }


  function padFileChkDir ( $file ) {

    $pados = strrpos($file, '/');
    $dir = substr($file, 0, $pados);
    
    if ( ! file_exists($dir) ) {

      padTimingStart ('write');

      mkdir ($dir, $GLOBALS ['padDirMode'], true);

      padTimingEnd ('write');

    }

  }


  function padFileChkFile ( $file ) {

    if ( ! file_exists($file) ) {

      padTimingStart ('write');
      
      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);
      
      padTimingEnd ('write');
  
    }

  }


?>