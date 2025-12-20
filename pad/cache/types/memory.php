<?php

  function padCacheInit ($url, $etag) {

    global $padCacheMemory, $padCacheMemoryHost, $padCacheMemoryPort;

    $padCacheMemory = new Memcached();
    $padCacheMemory->addServer($padCacheMemoryHost, $padCacheMemoryPort);

  }

  function padCacheEtag ($get) {

    global $padCacheMemory;

    return $padCacheMemory->get($get);

  }

  function padCacheUrl ($url) {

    global $padCacheMemory;

    return $padCacheMemory->get($url);

  }

  function padCacheGet ($etag) {

    global $padCacheMemory;

    return $padCacheMemory->get("x$etag");

  }

  function padCacheStore ($url, $etag, $data) {

    global $padCacheMemory, $padCacheServerAge, $padCacheServerNoData;

    $padCacheMemory->set($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);

    if ( ! $padCacheServerNoData ) {
      $padCacheMemory->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);
      $padCacheMemory->set("x$etag", $data,                          $padCacheServerAge+10);
     }

  }

  function padCacheUpdate ($url, $etag) {

    global $padCacheMemory, $padCacheServerAge, $padCacheServerNoData;

    $padCacheMemory->set($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);

    if ( ! $padCacheServerNoData ) {
      $padCacheMemory->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);
      $padCacheMemory->touch("x$etag", $padCacheServerAge+10);
    }

  }

  function padCacheDelete ($url, $etag) {

    global $padCacheMemory, $padCacheServerNoData;

    $padCacheMemory->delete($etag);

    if ( ! $padCacheServerNoData )
      $padCacheMemory->delete("x$etag");

  }

?>