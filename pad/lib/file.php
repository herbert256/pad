<?php


  function pad_file_get_contents ( $file ) {

    if ( ! pad_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");
    
    if ( ! file_exists($file) )
      return '';

    pad_timing_start ('read');
    $return = file_get_contents ($file);
    pad_timing_end ('read');

    return $return;    

  }


  function pad_file_put_contents ($in, $data='', $append=0) {

    $file = DATA . $in;

    if ( ! pad_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");

    return $file;

    pad_file_chk_dir  ( $file );
    pad_file_chk_file ( $file );

    if ( is_array($data) )
      $data = pad_json ($data);
      
    if ($data) {
      pad_timing_start ('write');
      if ($append) file_put_contents ($file, "$data\n", LOCK_EX | FILE_APPEND);
      else         file_put_contents ($file, $data, LOCK_EX);
      pad_timing_end ('write');
    }
    
  }


  function pad_file_valid_name ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_-]+$/', $file) ) return FALSE;
    if ( strpos($file, '//') !== FALSE )                 return FALSE;
    if ( strpos($file, '..') !== FALSE )                 return FALSE;

    if ( str_starts_with($file, PAD)  ) return TRUE;
    if ( str_starts_with($file, APPS) ) return TRUE;
    if ( str_starts_with($file, DATA) ) return TRUE;

    return FALSE;

  }


  function pad_file_chk_dir ( $file ) {

    $pos = strrpos($file, '/');
    $dir = substr($file, 0, $pos);
    
    if ( ! file_exists($dir) ) {

      pad_timing_start ('write');

      mkdir ($dir, $GLOBALS['pad_dir_mode'], true);

      pad_timing_end ('write');

    }

  }


  function pad_file_chk_file ( $file ) {

    if ( ! file_exists($file) ) {

      pad_timing_start ('write');
      
      touch($file);
      chmod($file, $GLOBALS['pad_file_mode']);
      
      pad_timing_end ('write');
  
    }

  }


?>