<?php

  // File backend for the page cache, under $padCacheFile: a url/<hash> file holds the
  // ETag of the page last built for that URL, an etag/<etag> file holds the body, and the
  // file's mtime is the entry's age.
  //
  // Implements the backend interface cache/inits.php and cache/exits.php call -
  // padCacheInit (nothing to open here), padCacheEtag, padCacheUrl, padCacheGet,
  // padCacheStore, padCacheUpdate, padCacheDelete - on top of the local helpers
  // padCacheExists, padCacheTouch, padCacheChkDir, padCacheDeleteFile and padCacheTime,
  // which prefix every path with $padCacheFile and create directories on demand.
  //
  // With $padCacheServerNoData only an empty timestamp file is kept per ETag, so the cache
  // can still answer 304 but never serves a body.

  function padCacheInit ($url, $etag) {}

  function padCacheEtag ($etag) {

    return ( padCacheExists ("etag/$etag") ) ? padCacheTime ("etag/$etag") : FALSE;

  }

  // The reads and writes go through $padCacheFile like every helper below: the bare
  // relative spellings this file started with made padFilePut root them under DATA/
  // itself, so the store wrote to DATA/url/ while every lookup read DATA/cache/url/,
  // and no entry ever came back.

  function padCacheUrl ($url) {

    global $padCacheFile;

    if ( padCacheExists ("url/$url") ) {
      $etag = padFileGet ($padCacheFile . "url/$url");
      if ( padCacheExists ("etag/$etag") )
        return [padCacheTime ("etag/$etag"), $etag];
    }

    return [];

  }

  function padCacheGet ($etag) {

    global $padCacheFile;

    return ( padCacheExists ("etag/$etag" ) ) ? padFileGet ($padCacheFile . "etag/$etag") : FALSE;

  }

  function padCacheStore ($url, $etag, $data) {

    global $padCacheFile, $padCacheServerNoData;

    padFilePut ($padCacheFile . "url/$url", $etag);

    if ( $padCacheServerNoData )
      padCacheTouch ("etag/$etag", $_SERVER['REQUEST_TIME']);
    else
      padFilePut ($padCacheFile . "etag/$etag", $data);

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