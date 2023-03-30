<?php


  function padExists ( $file ) {

    if ( ! padFileValidName ( $file ) )
      $return = FALSE;
    else
      $return = file_exists ( $file );

    if ( isset($GLOBALS ['padLog']) and $GLOBALS ['padLog'] )
      include pad . 'log/exists.php';

    return $return;

  }


  function padFileGetContents ( $file ) {

    if ( ! padFileValidName ( $file ) )
      return padError ("Invalid file name: $file");
    
    if ( ! padExists($file) )
      return '';

    padTimingStart ('read');
    $return = file_get_contents ($file);
    padTimingEnd ('read');

    return $return;    

  }


  function padFilePutContents ($in, $data='', $padAppend=0) {

    global $pad;
    
    $file = padData . str_replace(':', '_', $in);

    if ( ! padFileValidName ( $file ) )
      return padError ("Invalid file name: $file");

    padFileChkDir  ( $file );
    padFileChkFile ( $file );

    if ( is_array($data) or is_object($data) )
      $data = padJson ($data);
      
    if ($data) {
      padTimingStart ('write');
      if ($padAppend) file_put_contents ($file, "$data\n", LOCK_EX | FILE_padAppEND);
      else         file_put_contents ($file, $data,     LOCK_EX);
      padTimingEnd ('write');
    }
    
  }


  function padFileValidName ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_-]+$/', $file) ) return FALSE;
    if ( strpos($file, '//') !== FALSE )                  return FALSE;
    if ( strpos($file, '..') !== FALSE )                  return FALSE;
    if ( strpos($file, '/.') !== FALSE )                  return FALSE;
    if ( strpos($file, './') !== FALSE )                  return FALSE;

    if ( str_starts_with($file, pad)  ) return TRUE;
    if ( str_starts_with($file, padApps) ) return TRUE;
    if ( str_starts_with($file, padData) ) return TRUE;

    return FALSE;

  }


  function padFileChkDir ( $file ) {

    $dir = substr ( $file, 0, strrpos($file, '/') );
    
    if ( ! padExists($dir) ) {

      padTimingStart ('write');

      mkdir ($dir, $GLOBALS ['padDirMode'], true);

      padTimingEnd ('write');

    }

  }


  function padFileChkFile ( $file ) {

    if ( ! padExists($file) ) {

      padTimingStart ('write');
      
      touch($file);
      chmod($file, $GLOBALS ['padFileMode']);
      
      padTimingEnd ('write');
  
    }

  }


?>