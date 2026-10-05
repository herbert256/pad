<?php

  // Redis backend for the page cache: the ETag key holds the entry's age, the hashed URL
  // key holds [age, etag], and "x<etag>" holds the body, all written with
  // $padCacheServerAge as their expiry.
  //
  // Implements the padCacheInit/Etag/Url/Body/Store/Update/Delete interface that
  // cache/inits.php and cache/exits.php call. padCacheInit opens the connection and turns
  // on the PHP serializer, which is what lets padCacheUrl store an array. With
  // $padCacheServerNoData the body is not written, so the cache answers 304 from a client
  // ETag but never serves a body. The URL key is written all the same: cache/inits.php
  // finds the ETag it holds the client's against through the URL alone, and without it no
  // request was ever answered 304 in that mode.

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

  function padCacheBody ($etag) {

    global $padCacheRedis;

    return $padCacheRedis->get("x$etag");

  }

  function padCacheStore ($url, $etag, $data) {

    global $padCacheRedis, $padCacheServerAge, $padCacheServerNoData;

    $padCacheRedis->set($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);
    $padCacheRedis->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);

    if ( ! $padCacheServerNoData )
      $padCacheRedis->set("x$etag", $data, $padCacheServerAge+10);

  }

  function padCacheUpdate ($url, $etag) {

    global $padCacheRedis, $padCacheServerAge, $padCacheServerNoData;

    $padCacheRedis->set($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);
    $padCacheRedis->set($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);

    if ( ! $padCacheServerNoData )
      $padCacheRedis->expire("x$etag", $padCacheServerAge+10);

  }

  // The URL's entry goes with the ETag, as in the file cache: left behind it still named
  // the deleted ETag, as fresh as before, and a client holding that ETag was answered 304
  // for a page that is no longer stored - under $padCacheServerNoData, where every request
  // builds the page, any page that stopped being storable.

  function padCacheDelete ($url, $etag) {

    global $padCacheRedis, $padCacheServerNoData;

    $padCacheRedis->del($etag);
    $padCacheRedis->del($url);

    if ( ! $padCacheServerNoData )
      $padCacheRedis->del("x$etag");

  }

?>