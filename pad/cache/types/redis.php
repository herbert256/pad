<?php

  function padCacheInit ($url, $etag) {

    global $padCacheRedis, $padCacheRedisHost, $padCacheRedisPort;

    $padCacheRedis = new Redis();
    $padCacheRedis->connect($padCacheRedisHost, $padCacheRedisPort);
    $padCacheRedis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);

  }

  function padCacheEtag ($get) {

    global $padCacheRedis;

    return $padCacheRedis->get($get);

  }

  function padCacheUrl ($url) {

    global $padCacheRedis;

    return $padCacheRedis->get($url);

  }

  function padCacheGet ($etag) {

    global $padCacheRedis;

    return $padCacheRedis->get("x$etag");

  }

  function padCacheStore ($url, $etag, $data) {

    global $padCacheRedis, $padCacheServerAge, $padCacheServerNoData;

    $padCacheRedis->set($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);

    if ( ! $padCacheServerNoData ) {
      $padCacheRedis->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);
      $padCacheRedis->set("x$etag", $data,                          $padCacheServerAge+10);
    }

  }

  function padCacheUpdate ($url, $etag) {

    global $padCacheRedis, $padCacheServerAge, $padCacheServerNoData;

    $padCacheRedis->set($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);

    if ( ! $padCacheServerNoData ) {
      $padCacheRedis->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);
      $padCacheRedis->expire("x$etag", $padCacheServerAge+10);
    }

  }

  function padCacheDelete ($url, $etag) {

    global $padCacheRedis, $padCacheServerNoData;

    $padCacheRedis->del($etag);

    if ( ! $padCacheServerNoData )
      $padCacheRedis->del("x$etag");

  }

?>