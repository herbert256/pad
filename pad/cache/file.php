<?php


  function pCache_init ($url, $etag) {}


  function pCache_etag ($etag) {
    
    return ( pCache_exists ("etag/$etag") ) ? pCache_time ("etag/$etag") : FALSE;

  }

  
  function pCache_url ($url) {

    if ( pCache_exists ("url/$url") ) {
      $etag = pCache_get_contents("url/$url");
      if ( pCache_exists ("etag/$etag") )
        return [pCache_time ("etag/$etag"), $etag];
    }

    return [];

  }


  function pCache_get ($etag) {

    return ( pCache_exists ("etag/$etag" ) ) ? pCache_get_contents("etag/$etag") : FALSE;

  }


  function pCache_store ($url, $etag, $data) {
    
    pCache_put_contents ("url/$url", $etag);

    if ( $GLOBALS ['padCache_server_no_data'] )
      pCache_touch ("etag/$etag", $_SERVER['REQUEST_TIME']);
    else
      pCache_put_contents ("etag/$etag", $data);

  }

  
  function pCache_update ($url, $etag) {

    pCache_touch ("etag/$etag", $_SERVER['REQUEST_TIME']);
    
  }


  function pCache_delete ($url, $etag) {

    pCache_delete_file ("url/$url");
    pCache_delete_file ("etag/$etag");

  }


  function  pCache_exists ( $file ) {

    $file = $GLOBALS ['padCache_file'] . $file;

    $return = file_exists ($file);

    return $return;    

  }


  function pCache_get_contents ( $file ) {

    $file = $GLOBALS ['padCache_file'] . $file;

    $return = pFile_get_contents ($file);

    return $return;    

  }


  function pCache_put_contents ($file, $data) {
   
    pCache_chk_dir($file);

    $file = $GLOBALS ['padCache_file'] . $file;
    
    file_put_contents ($file, $data, LOCK_EX);
    
  }


  function pCache_touch ($file, $time) {

    pCache_chk_dir ($file);

    $file = $GLOBALS ['padCache_file'] . $file;
    
    touch ( $file, $time );

  }
  

  function pCache_chk_dir ($file) {

    $file = $GLOBALS ['padCache_file'] . $file;
    $dir  = substr($file, 0, strrpos($file, '/'));
    
    if ( ! file_exists ($dir) )
      mkdir($dir, $GLOBALS ['padDir_mode'], true);
  
  }

  function pCache_delete_file ($file) {

    $file = $GLOBALS ['padCache_file'] . $file;
    
    if ( file_exists($file) )
      unlink ($file);

  }


  function pCache_time ($file) {

    $file = $GLOBALS ['padCache_file'] . $file;
    
    if ( file_exists($file) )
      return filemtime($file);

    return 0;

  }


?>