<?php

  // Memcached backend for the page cache, the default $padCacheServerType: the ETag key
  // holds the entry's age, the hashed URL key holds [age, etag], and "x<etag>" holds the
  // body, all written with $padCacheServerAge as their expiry.
  //
  // Implements the padCacheInit/Etag/Url/Get/Store/Update/Delete interface that
  // cache/inits.php and cache/exits.php call; padCacheInit opens the connection from the
  // $padCacheMemcached* settings. With $padCacheServerNoData only the age key is written,
  // so the cache answers 304 from a client ETag but never serves a body.

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

    if ( ! $padCacheServerNoData ) {
      $padCacheMemcached->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);
      $padCacheMemcached->set("x$etag", $data,                          $padCacheServerAge+10);
     }

  }

  function padCacheUpdate ($url, $etag) {

    global $padCacheMemcached, $padCacheServerAge, $padCacheServerNoData;

    $padCacheMemcached->set($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);

    if ( ! $padCacheServerNoData ) {
      $padCacheMemcached->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);
      $padCacheMemcached->touch("x$etag", $padCacheServerAge+10);
    }

  }

  function padCacheDelete ($url, $etag) {

    global $padCacheMemcached, $padCacheServerNoData;

    $padCacheMemcached->delete($etag);

    if ( ! $padCacheServerNoData )
      $padCacheMemcached->delete("x$etag");

  }

?>