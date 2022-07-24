<?php


  function pad_file_trace ( $operation, $value ) {

    if ( $GLOBALS['pad_exit'] <> 1 )
      return;
  
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 2) [1] );

    $file = DATA . $GLOBALS['pad_trace_dir_occ'] . '/file.txt';

    $dir = substr($file, 0, strrpos($file, '/'));
    if ( ! pad_file_trace_exists ($dir) ) {
      pad_timing_start ('write');
      mkdir ($dir, $GLOBALS['pad_dir_mode'], true);
      pad_timing_end ('write');
    }

    if ( ! pad_file_trace_exists ($file) ) {
      pad_timing_start ('write');
      touch($file);
      chmod($file, $GLOBALS['pad_file_mode']);
      pad_timing_end ('write');
    }

    pad_timing_start ('write');
    file_put_contents ( $file, "$operation: $file:$line -> $value\n", FILE_APPEND | LOCK_EX );
    pad_timing_end ('write');

  }


  function pad_file_trace_exists ( $file ) {

    pad_timing_start ('read');

    if ( file_exists ($file) ) {
      pad_timing_end ('read');
      return TRUE;
    }
    else {
      pad_timing_end ('read');
      return FALSE;
    }

  }


  function pad_file_valid_name ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_#:-]+$/', $file) ) return FALSE;
    if ( strpos($file, '//') !== FALSE )                    return FALSE;
    if ( strpos($file, '..') !== FALSE )                    return FALSE;

    if ( str_starts_with($file, PAD)  ) return TRUE;
    if ( str_starts_with($file, APPS) ) return TRUE;
    if ( str_starts_with($file, DATA) ) return TRUE;

    return FALSE;

  }


  function pad_file_exists ( $file ) {

    if ($GLOBALS['pad_trace_file'])
      pad_file_trace ( 'exists', $file );

    if ( ! pad_file_valid_name ( $file ) )
      return FALSE;

    pad_timing_start ('read');

    if ( file_exists ($file) ) {
      pad_timing_end ('read');
      return TRUE;
    }
    else {
      pad_timing_end ('read');
      return FALSE;
    }

  }


  function pad_file_get_contents ( $file ) {

    if ($GLOBALS['pad_trace_file'])
      pad_file_trace ( 'get...', $file );

    if ( ! pad_file_exists($file) )
      return '';

    pad_timing_start ('read');
    $return = file_get_contents ($file);
    pad_timing_end ('read');

    return $return;    

  }


  function pad_file_name ($file, $action) {

    if ($GLOBALS['pad_trace_file'])
      pad_file_trace ( $action, $file );

    $file = str_replace('//', '/', $file);
    
    if ( substr($file, 0, 1) == '/')
      $file = substr($file, 1);

    $file = DATA . $file;

    if ( ! pad_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");

    return $file;

  }
   

  function pad_file_chk_dir ( $file ) {

    $pos = strrpos($file, '/');
    $dir = substr($file, 0, $pos);
    
    if ( ! pad_file_exists($dir) ) {

      pad_timing_start ('write');

      mkdir ($dir, $GLOBALS['pad_dir_mode'], true);

      pad_timing_end ('write');

    }

  }

  function pad_file_chk_file ( $file ) {

    if ( ! pad_file_exists($file) ) {

      pad_timing_start ('write');
      
      touch($file);
      chmod($file, $GLOBALS['pad_file_mode']);
      
      pad_timing_end ('write');
  
    }

  }


  function pad_file_put_contents ($in, $data='', $append=0) {

    if ( $GLOBALS['app'] == 'pad' and strpos($in, 'trace/') !== FALSE )
      return;

    $file = pad_file_name ( $in, 'put...' );

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


?>