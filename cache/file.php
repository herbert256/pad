<?php


  function pad_cache_init_file ($url, $etag) {

  }


  function pad_cache_etag_file ($etag) {
    
    global $pad_cache_file;

    return ( pad_cache_file_exists ("$pad_cache_file/etag/$etag") ) ? pad_cache_file_time ("$pad_cache_file/etag/$etag") : FALSE;

  }

  
  function pad_cache_url_file ($url) {

    global $pad_cache_file;

    if ( pad_cache_file_exists ("$pad_cache_file/url/$url") ) {
      $etag = pad_cache_file_get_contents("$pad_cache_file/url/$url");
      if ( pad_cache_file_exists ("$pad_cache_file/etag/$etag") )
        return [0 => pad_cache_file_time ("$pad_cache_file/etag/$etag"), 1 => $etag];
    }

    return [];

  }


  function pad_cache_get_file ($etag) {

    global $pad_cache_file;

    return ( pad_cache_file_exists ("$pad_cache_file/etag/$etag" ) ) ? pad_cache_file_get_contents("$pad_cache_file/etag/$etag") : FALSE;

  }


  function pad_cache_store_file ($url, $etag, $data) {

    global $pad_cache_file;
    
    pad_cache_file_put_contents ("$pad_cache_file/url/$url", $etag, LOCK_EX);

    if ( $GLOBALS['pad_cache_server_no_data'] )
      pad_cache_file_touch ("$pad_cache_file/etag/$etag", $_SERVER['REQUEST_TIME']);
    else
      pad_cache_file_put_contents ("$pad_cache_file/etag/$etag", $data, LOCK_EX);

  }

  
  function pad_cache_update_file ($url, $etag) {

    global $pad_cache_file;
 
    pad_cache_file_touch ("$pad_cache_file/etag/$etag", $_SERVER['REQUEST_TIME']);
    
  }


  function pad_cache_delete_file ($url, $etag) {

    global $pad_cache_file;

    pad_cache_file_delete ("$pad_cache_file/url/$url");
    pad_cache_file_delete ("$pad_cache_file/etag/$etag");

  }
  

  function pad_cache_file_valid_name ( $file ) {

    if ( ! preg_match ('/^[A-Za-z0-9\.\/_]+$/', $file) ) return FALSE;
    if ( substr($file,0,1) <> '/' )                      return FALSE;
    if ( strpos($file, '//') !== FALSE )                 return FALSE;
    if ( strpos($file, '..') !== FALSE )                 return FALSE;

    if ( str_starts_with($file, PAD_HOME) ) return TRUE;
    if ( str_starts_with($file, PAD_APPS) ) return TRUE;
    if ( str_starts_with($file, PAD_DATA) ) return TRUE;

    return FALSE;

  }


  function pad_cache_file_exists ( $file ) {

    if ( ! pad_cache_file_valid_name ( $file ) )
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


  function pad_cache_file_get_contents ( $file ) {

    if ( ! pad_cache_file_exists($file) )
      return '';

    pad_timing_start ('read');
    $return = file_get_contents ($file);
    pad_timing_end ('read');

    return $return;    

  }


  function pad_cache_file_put_contents ($file, $data, $append=0) {

    $file = PAD_DATA . $file;

    if ( ! pad_cache_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");
   
    pad_cache_file_create ($file);

    pad_timing_start ('write');
    if ($append) file_put_contents ($file, $data, LOCK_EX | FILE_APPEND);
    else         file_put_contents ($file, $data, LOCK_EX);
    pad_timing_end ('write');
    
  }


  function pad_cache_file_touch ($file, $time) {

    $file = PAD_DATA . $file;

    if ( ! pad_cache_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");

    if ( ! file_exists($file) )
      pad_cache_file_create ($file);

    pad_timing_start ('write');
    touch ( $file, $time );
    pad_timing_end ('write');

  }
  

  function pad_cache_file_create ($file) {

    $pos = strrpos($file, '/');
    $dir = substr($file, 0, $pos);
    
    if ( ! file_exists($dir) ) {
      pad_timing_start ('write');
      mkdir($dir, $GLOBALS['pad_dir_mode'], true);
      pad_timing_end ('write');
    }
    
    if ( ! file_exists($file) ) {
      pad_timing_start ('write');
      touch($file);
      chmod($file, $GLOBALS['pad_cache_file_mode']);
      pad_timing_end ('write');
    }
  
  }

  function pad_cache_file_delete ($file) {

    if ( ! pad_cache_file_valid_name ( $file ) )
      return pad_error ("Invalid file name: $file");

    if ( ! file_exists($file) ) {
      pad_timing_start ('write');
      unlink ($file);
      pad_timing_end ('write');
    }

  }


  function pad_cache_file_time ($file) {

    if ( ! pad_cache_file_valid_name ( $file ) )
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