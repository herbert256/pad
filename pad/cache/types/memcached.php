<?php

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
