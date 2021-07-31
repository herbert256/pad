<?php


  function pad_cache_init ($url, $etag) {}


  function pad_cache_etag ($etag) {
    
    return ( pad_cache_exists ("etag/$etag") ) ? pad_cache_time ("etag/$etag") : FALSE;

  }

  
  function pad_cache_url ($url) {

    if ( pad_cache_exists ("url/$url") ) {
      $etag = pad_cache_get_contents("url/$url");
      if ( pad_cache_exists ("etag/$etag") )
        return [pad_cache_time ("etag/$etag"), $etag];
    }

    return [];

  }


  function pad_cache_get ($etag) {

    return ( pad_cache_exists ("etag/$etag" ) ) ? pad_cache_get_contents("etag/$etag") : FALSE;

  }


  function pad_cache_store ($url, $etag, $data) {
    
    pad_cache_put_contents ("url/$url", $etag);

    if ( $GLOBALS['pad_cache_server_no_data'] )
      pad_cache_touch ("etag/$etag", $_SERVER['REQUEST_TIME']);
    else
      pad_cache_put_contents ("etag/$etag", $data);

  }

  
  function pad_cache_update ($url, $etag) {

    pad_cache_touch ("etag/$etag", $_SERVER['REQUEST_TIME']);
    
  }


  function pad_cache_delete ($url, $etag) {

    pad_cache_delete_file ("url/$url");
    pad_cache_delete_file ("etag/$etag");

  }


  function pad_cache_exists ( $file ) {

    $file = $GLOBALS ['pad_cache_file'] . $file;

    $return = file_exists ($file);

    return $return;    

  }


  function pad_cache_get_contents ( $file ) {

    $file = $GLOBALS ['pad_cache_file'] . $file;

    $return = file_get_contents ($file);

    return $return;    

  }


  function pad_cache_put_contents ($file, $data) {
   
    pad_cache_create ($file);

    $file = $GLOBALS ['pad_cache_file'] . $file;
    
    file_put_contents ($file, $data, LOCK_EX);
    
  }


  function pad_cache_touch ($file, $time) {

    pad_cache_create ($file);

    $file = $GLOBALS ['pad_cache_file'] . $file;
    
    touch ( $file, $time );

  }
  

  function pad_cache_create ($file) {

    $file = $GLOBALS ['pad_cache_file'] . $file;
    $dir  = substr($file, 0, strrpos($file, '/'));
    
    if ( ! file_exists($dir) )
      mkdir($dir, $GLOBALS['pad_dir_mode'], true);
  
  }

  function pad_cache_delete_file ($file) {

    $file = $GLOBALS ['pad_cache_file'] . $file;
    
    if ( file_exists($file) )
      unlink ($file);

  }


  function pad_cache_time ($file) {

    $file = $GLOBALS ['pad_cache_file'] . $file;
    
    if ( file_exists($file) )
      return filemtime($file);

    return 0;

  }


?>