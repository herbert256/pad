<?php


  function pad_cache_init_file ($url, $etag) {

  }


  function pad_cache_etag_file ($etag) {
    
    global $pad_cache_file;

    return ( file_exists ("$pad_cache_file/etag/$etag") ) ? filemtime("$pad_cache_file/etag/$etag") : FALSE;

  }

  
  function pad_cache_url_file ($url) {

    global $pad_cache_file;

    if ( file_exists ("$pad_cache_file/url/$url") ) {
      $etag = file_get_contents("$pad_cache_file/url/$url");
      if ( file_exists ("$pad_cache_file/etag/$etag") )
        return [0 => filemtime("$pad_cache_file/etag/$etag"), 1 => $etag];
    }

    return [];

  }


  function pad_cache_get_file ($etag) {

    global $pad_cache_file;

    return ( file_exists ("$pad_cache_file/etag/$etag" ) ) ? file_get_contents("$pad_cache_file/etag/$etag") : FALSE;

  }


  function pad_cache_store_file ($url, $etag, $data) {

    global $pad_cache_file;
    
    pad_timing_start ('write');
    
    pad_check_file ("$pad_cache_file/url/$url");
    file_put_contents ("$pad_cache_file/url/$url", $etag, LOCK_EX);

    pad_check_file ("$pad_cache_file/etag/$etag");

    if ( $GLOBALS['pad_cache_server_no_data'] )
      touch ("$pad_cache_file/etag/$etag", $_SERVER['REQUEST_TIME']);
    else
      file_put_contents ("$pad_cache_file/etag/$etag", $data, LOCK_EX);

    pad_timing_end ('write');

  }

  
  function pad_cache_update_file ($url, $etag) {

    global $pad_cache_file;
 
    touch ("$pad_cache_file/etag/$etag", $_SERVER['REQUEST_TIME']);
    
  }


  function pad_cache_delete_file ($url, $etag) {

    global $pad_cache_file;

    @unlink ("$pad_cache_file/url/$url");
    @unlink ("$pad_cache_file/etag/$etag");

  }
  

?>