<?php


  function padCacheInit ($url, $etag) {}


  function padCacheEtag ($etag) {
    
    return ( padCacheExists ("etag/$etag") ) ? padCacheTime ("etag/$etag") : FALSE;

  }

  
  function padCacheUrl ($url) {

    if ( padCacheExists ("url/$url") ) {
      $etag = padCacheGetContents("url/$url");
      if ( padCacheExists ("etag/$etag") )
        return [padCacheTime ("etag/$etag"), $etag];
    }

    return [];

  }


  function padCacheGet ($etag) {

    return ( padCacheExists ("etag/$etag" ) ) ? padCacheGetContents("etag/$etag") : FALSE;

  }


  function padCacheStore ($url, $etag, $data) {
    
    padCachePutContents ("url/$url", $etag);

    if ( $GLOBALS ['padCacheServerNoData'] )
      padCacheTouch ("etag/$etag", $_SERVER['REQUEST_TIME']);
    else
      padCachePutContents ("etag/$etag", $data);

  }

  
  function padCacheUpdate ($url, $etag) {

    padCacheTouch ("etag/$etag", $_SERVER['REQUEST_TIME']);
    
  }


  function padCacheDelete ($url, $etag) {

    padCacheDeleteFile ("url/$url");
    padCacheDeleteFile ("etag/$etag");

  }


  function  padCacheExists ( $file ) {

    $file = $GLOBALS ['padCacheFile'] . $file;

    $return = file_exists ($file);

    return $return;    

  }


  function padCacheGetContents ( $file ) {

    $file = $GLOBALS ['padCacheFile'] . $file;

    $return = padFileGetContents ($file);

    return $return;    

  }


  function padCachePutContents ($file, $data) {
   
    padCacheChkDir($file);

    $file = $GLOBALS ['padCacheFile'] . $file;
    
    file_put_contents ($file, $data, LOCK_EX);
    
  }


  function padCacheTouch ($file, $time) {

    padCacheChkDir ($file);

    $file = $GLOBALS ['padCacheFile'] . $file;
    
    touch ( $file, $time );

  }
  

  function padCacheChkDir ($file) {

    $file = $GLOBALS ['padCacheFile'] . $file;
    $dir  = substr($file, 0, strrpos($file, '/'));
    
    if ( ! file_exists ($dir) )
      mkdir($dir, $GLOBALS ['padDirMode'], true);
  
  }

  function padCacheDeleteFile ($file) {

    $file = $GLOBALS ['padCacheFile'] . $file;
    
    if ( file_exists($file) )
      unlink ($file);

  }


  function padCacheTime ($file) {

    $file = $GLOBALS ['padCacheFile'] . $file;
    
    if ( file_exists($file) )
      return filemtime($file);

    return 0;

  }


?>