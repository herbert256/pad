<?php


  function pad_remove_dir ($dir) { 
  
   if (is_dir($dir)) { 
     $objects = scandir($dir);
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
           pad_remove_dir ($dir. DIRECTORY_SEPARATOR .$object);
         else
           unlink($dir. DIRECTORY_SEPARATOR .$object); 
       } 
     }
     rmdir($dir); 
   } 
 
  }


  function pad_trace_file_operations ( $operation, $value ) {

    pad_trace_write ( '', 'file_operations.txt', "$operation: $file:$line -> $value\n" );

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

    if ($GLOBALS['pad_trace_file_operations'])
      pad_trace_file_operations( 'exists', $file );

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

    if ($GLOBALS['pad_trace_file_operations'])
      pad_trace_file_operations( 'get...', $file );

    if ( ! pad_file_exists($file) )
      return '';

    pad_timing_start ('read');
    $return = file_get_contents ($file);
    pad_timing_end ('read');

    return $return;    

  }


  function pad_file_put_contents ($file, $data='', $append=0) {

    if ($GLOBALS['pad_trace_file_operations'])
      pad_trace_file_operations( 'put...', $file );

    $file = str_replace('//', '/', $file);
    
    if ( is_array($data) )
      $data = pad_json ($data);

    if ( substr($file, 0, 1) == '/')
      $file = substr($file, 1);

    $file = DATA . $file;

    if ( ! pad_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");
   
    $pos = strrpos($file, '/');
    $dir = substr($file, 0, $pos);
    
    if ( ! pad_file_exists($dir) ) {
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

    if ($data) {
      pad_timing_start ('write');
      if ($append) file_put_contents ($file, "$data\n", LOCK_EX | FILE_APPEND);
      else         file_put_contents ($file, $data, LOCK_EX);
      pad_timing_end ('write');
    }
    
  }


?>