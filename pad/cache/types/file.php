<?php

  function padCacheInit ($url, $etag) {}

  function padCacheEtag ($etag) {

    return ( padCacheExists ("etag/$etag") ) ? padCacheTime ("etag/$etag") : FALSE;

  }

  function padCacheUrl ($url) {

    if ( padCacheExists ("url/$url") ) {
      $etag = padFileGet ("url/$url");
      if ( padCacheExists ("etag/$etag") )
        return [padCacheTime ("etag/$etag"), $etag];
    }

    return [];

  }

  function padCacheGet ($etag) {

    return ( padCacheExists ("etag/$etag" ) ) ? padFileGet ("etag/$etag") : FALSE;

  }

  function padCacheStore ($url, $etag, $data) {

    global $padCacheServerNoData;

    padFilePut ("url/$url", $etag);

    if ( $padCacheServerNoData )
      padCacheTouch ("etag/$etag", $_SERVER['REQUEST_TIME']);
    else
      padFilePut ("etag/$etag", $data);

  }

  function padCacheUpdate ($url, $etag) {

    padCacheTouch ("etag/$etag", $_SERVER['REQUEST_TIME']);

  }

  function padCacheDelete ($url, $etag) {

    padCacheDeleteFile ("url/$url");
    padCacheDeleteFile ("etag/$etag");

  }

  function  padCacheExists ( $file ) {

    global $padCacheFile;

    $file = $padCacheFile . $file;

    $return = file_exists ($file);

    return $return;

  }

  function padCacheTouch ($file, $time) {

    global $padCacheFile;

    padCacheChkDir ($file);

    $file = $padCacheFile . $file;

    touch ( $file, $time );

  }

  function padCacheChkDir ($file) {

    global $padCacheFile, $padDirMode;

    $file = $padCacheFile . $file;
    $dir  = substr($file, 0, strrpos($file, '/'));

    if ( ! file_exists ($dir) )
      mkdir($dir, $padDirMode, true);

  }

  function padCacheDeleteFile ($file) {

    global $padCacheFile;

    $file = $padCacheFile . $file;

    if ( file_exists($file) )
      unlink ($file);

  }

  function padCacheTime ($file) {

    global $padCacheFile;

    $file = $padCacheFile . $file;

    if ( file_exists($file) )
      return filemtime($file);

    return 0;

  }

?>
