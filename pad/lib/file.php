<?php


  function pFile_get_contents ( $file ) {

    if ( ! pFile_valid_name ( $file ) )
      return pError ("Invalid file name: $file");
    
    if ( ! file_exists($file) )
      return '';

    pTiming_start ('read');
    $return = file_get_contents ($file);
    pTiming_end ('read');

    return $return;    

  }


  function pFile_put_contents ($in, $data='', $append=0) {

    $file = DATA . $in;

    if ( ! pFile_valid_name ( $file ) )
      return pError ("Invalid file name: $file");

    pFile_chk_dir  ( $file );
    pFile_chk_file ( $file );

    if ( is_array($data) )
      $data = pJson ($data);
      
    if ($data) {
      pTiming_start ('write');
      if ($append) file_put_contents ($file, "$data\n", LOCK_EX | FILE_APPEND);
      else         file_put_contents ($file, $data, LOCK_EX);
      pTiming_end ('write');
    }
    
  }


  function pFile_valid_name ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_-]+$/', $file) ) return FALSE;
    if ( strpos($file, '//') !== FALSE )                  return FALSE;
    if ( strpos($file, '..') !== FALSE )                  return FALSE;

    if ( str_starts_with($file, PAD)  ) return TRUE;
    if ( str_starts_with($file, APPS) ) return TRUE;
    if ( str_starts_with($file, DATA) ) return TRUE;

    return FALSE;

  }


  function pFile_chk_dir ( $file ) {

    $pados = strrpos($file, '/');
    $dir = substr($file, 0, $pados);
    
    if ( ! file_exists($dir) ) {

      pTiming_start ('write');

      mkdir ($dir, $GLOBALS ['padDir_mode'], true);

      pTiming_end ('write');

    }

  }


  function pFile_chk_file ( $file ) {

    if ( ! file_exists($file) ) {

      pTiming_start ('write');
      
      touch($file);
      chmod($file, $GLOBALS ['padFile_mode']);
      
      pTiming_end ('write');
  
    }

  }


?>