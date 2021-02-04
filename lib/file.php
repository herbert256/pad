<?php


  function pad_file_valid_name ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_]+$/', $file) ) return FALSE;
    if ( substr($file,0,1) <> '/' )                      return FALSE;
    if ( strpos($file, '//') !== FALSE )                 return FALSE;
    if ( strpos($file, '..') !== FALSE )                 return FALSE;

    if ( str_starts_with($file, PAD_HOME) ) return TRUE;
    if ( str_starts_with($file, PAD_APPS) ) return TRUE;
    if ( str_starts_with($file, PAD_DATA) ) return TRUE;

    return FALSE;

  }


  function pad_file_exists ( $file ) {

    if ( ! pad_file_valid_name ( $file ) ) {
      pad_trace ('exists/invalid', $file);
      return FALSE;
    }

    pad_timing_start ('read');

    if ( file_exists ($file) ) {
      pad_timing_end ('read');
      pad_trace ('exists/true', $file);
      return TRUE;
    }
    else {
      pad_timing_end ('read');
      pad_trace ('exists/false', $file);
      return FALSE;
    }

  }


  function pad_file_get_contents ( $file ) {

    if ( ! pad_file_exists($file) )
      return '';

    pad_timing_start ('read');
    $return = file_get_contents ($file);
    pad_timing_end ('read');

    pad_trace ('file/read', $file);

    return $return;    

  }


  function pad_file_create ($file) {

    $pos = strrpos($file, '/');
    $dir = substr($file, 0, $pos);
    
    if ( ! file_exists($dir) ) {
      pad_timing_start ('write');
      mkdir($dir, $GLOBALS['pad_dir_mode'], true);
      pad_timing_end ('write');
    }
    
    if ( ! pad_file_exists($file) ) {
      pad_timing_start ('write');
      touch($file);
      chmod($file, $GLOBALS['pad_file_mode']);
      pad_timing_end ('write');
    }
  
  }


  function pad_file_put_contents ($file, $data, $append=0) {

    if ( str_starts_with($file, PAD_DATA) )
      $file = str_replace(PAD_DATA, '', $file);

    $file = PAD_DATA . $file;

    if ( ! pad_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");
   
    pad_file_create ($file);

    pad_timing_start ('write');
    if ($append) file_put_contents ($file, $data, LOCK_EX | FILE_APPEND);
    else         file_put_contents ($file, $data, LOCK_EX);
    pad_timing_end ('write');
    
  }


  function pad_file_touch ($file, $time) {

    if ( ! pad_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");

    if ( ! file_exists($file) )
      pad_file_create ($file);

    pad_timing_start ('write');
    touch ( $file, $time );
    pad_timing_end ('write');

  }
  

  function pad_file_delete ($file) {

    if ( ! pad_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");

    if ( ! file_exists($file) ) {
      pad_timing_start ('write');
      unlink ($file);
      pad_timing_end ('write');
    }

  }


  function pad_file_time ($file) {

    if ( ! pad_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");

    if ( file_exists($file) ) {
      pad_timing_start ('read');
      $return =filemtime("$pad_cache_file/etag/$etag");
      pad_timing_end ('read');
      return $return;
    }

    return 0;

  }


?>