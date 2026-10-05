<?php

  // Memcached backend for the page cache, the default $padCacheServerType: the ETag key
  // holds the entry's age, the hashed URL key holds [age, etag], and "x<etag>" holds the
  // body, all written with $padCacheServerAge as their expiry.
  //
  // Implements the padCacheInit/Etag/Url/Get/Store/Update/Delete interface that
  // cache/inits.php and cache/exits.php call; padCacheInit opens the connection from the
  // $padCacheMemcached* settings. With $padCacheServerNoData the body is not written, so
  // the cache answers 304 from a client ETag but never serves a body. The URL key is
  // written all the same: cache/inits.php finds the ETag it holds the client's against
  // through the URL alone, and without it no request was ever answered 304 in that mode.

  function padCacheInit ($url, $etag) {

    global $padCacheMemcached, $padCacheMemcachedHost, $padCacheMemcachedPort;

    $padCacheMemcached = new Memcached();
    $padCacheMemcached->addServer($padCacheMemcachedHost, $padCacheMemcachedPort);

  }

  function padCacheEtag ($get) {

    global $padCacheMemcached;

    return $padCacheMemcached->get($get);

  }

  function padCacheUrl ($url) {

    global $padCacheMemcached;

    return $padCacheMemcached->get($url);

  }

  function padCacheGet ($etag) {

    global $padCacheMemcached;

    return $padCacheMemcached->get("x$etag");

  }

  function padCacheStore ($url, $etag, $data) {

    global $padCacheMemcached, $padCacheServerAge, $padCacheServerNoData;

    $padCacheMemcached->set($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);
    $padCacheMemcached->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);

    if ( ! $padCacheServerNoData )
      $padCacheMemcached->set("x$etag", $data, $padCacheServerAge+10);

  }

  function padCacheUpdate ($url, $etag) {

    global $padCacheMemcached, $padCacheServerAge, $padCacheServerNoData;

    $padCacheMemcached->set($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);
    $padCacheMemcached->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);

    if ( ! $padCacheServerNoData )
      $padCacheMemcached->touch("x$etag", $padCacheServerAge+10);

  }

  // The URL's entry goes with the ETag, as in the file cache: left behind it still named
  // the deleted ETag, as fresh as before, and a client holding that ETag was answered 304
  // for a page that is no longer stored - under $padCacheServerNoData, where every request
  // builds the page, any page that stopped being storable.

  function padCacheDelete ($url, $etag) {

    global $padCacheMemcached, $padCacheServerNoData;

    $padCacheMemcached->delete($etag);
    $padCacheMemcached->delete($url);

    if ( ! $padCacheServerNoData )
      $padCacheMemcached->delete("x$etag");

  }

?>