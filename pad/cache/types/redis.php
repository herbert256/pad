<?php

  // Redis backend for the page cache: the ETag key holds the entry's age, the hashed URL
  // key holds [age, etag], and "x<etag>" holds the body, all written with
  // $padCacheServerAge as their expiry.
  //
  // Implements the padCacheInit/Etag/Url/Get/Store/Update/Delete interface that
  // cache/inits.php and cache/exits.php call. padCacheInit opens the connection and turns
  // on the PHP serializer, which is what lets padCacheUrl store an array. With
  // $padCacheServerNoData only the age key is written, so the cache answers 304 from a
  // client ETag but never serves a body.

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