<?php


  function pad_cache_init_file ($url, $etag) {

  }


  function pad_cache_etag_file ($etag) {
    
    global $pad_cache_file;

    return ( pad_file_exists ("$pad_cache_file/etag/$etag") ) ? pad_file_time ("$pad_cache_file/etag/$etag") : FALSE;

  }

  
  function pad_cache_url_file ($url) {

    global $pad_cache_file;

    if ( pad_file_exists ("$pad_cache_file/url/$url") ) {
      $etag = pad_file_get_contents("$pad_cache_file/url/$url");
      if ( pad_file_exists ("$pad_cache_file/etag/$etag") )
        return [0 => pad_file_time ("$pad_cache_file/etag/$etag"), 1 => $etag];
    }

    return [];

  }


  function pad_cache_get_file ($etag) {

    global $pad_cache_file;

    return ( pad_file_exists ("$pad_cache_file/etag/$etag" ) ) ? pad_file_get_contents("$pad_cache_file/etag/$etag") : FALSE;

  }


  function pad_cache_store_file ($url, $etag, $data) {

    global $pad_cache_file;
    
    pad_file_put_contents ("$pad_cache_file/url/$url", $etag, LOCK_EX);

    if ( $GLOBALS['pad_cache_server_no_data'] )
      pad_file_touch ("$pad_cache_file/etag/$etag", $_SERVER['REQUEST_TIME']);
    else
      pad_file_put_contents ("$pad_cache_file/etag/$etag", $data, LOCK_EX);

  }

  
  function pad_cache_update_file ($url, $etag) {

    global $pad_cache_file;
 
    pad_file_touch ("$pad_cache_file/etag/$etag", $_SERVER['REQUEST_TIME']);
    
  }


  function pad_cache_delete_file ($url, $etag) {

    global $pad_cache_file;

    pad_file_delete ("$pad_cache_file/url/$url");
    pad_file_delete ("$pad_cache_file/etag/$etag");

  }
  

?>