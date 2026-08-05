<?php

  // APCu backend for the page cache, i.e. the local shared-memory one: the ETag key holds
  // the entry's age, the hashed URL key holds [age, etag], and "x<etag>" holds the body,
  // all written with $padCacheServerAge as their TTL.
  //
  // Implements the padCacheInit/Etag/Url/Get/Store/Update/Delete interface that
  // cache/inits.php and cache/exits.php call; padCacheInit has nothing to open, APCu is
  // simply there. With $padCacheServerNoData only the age key is written, so the cache
  // answers 304 from a client ETag but never serves a body.

  function padCacheInit ($url, $etag) {}

  function padCacheEtag ($get) {

    return apcu_fetch($get);

  }

  function padCacheUrl ($url) {

    return apcu_fetch($url);

  }

  function padCacheGet ($etag) {

    return apcu_fetch("x$etag");

  }

  function padCacheStore ($url, $etag, $data) {

    global $padCacheServerAge, $padCacheServerNoData;

    apcu_store($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);

    if ( ! $padCacheServerNoData ) {
      apcu_store($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);
      apcu_store("x$etag", $data,                          $padCacheServerAge+10);
    }

  }

  function padCacheUpdate ($url, $etag) {

    global $padCacheServerAge, $padCacheServerNoData;

    apcu_store($etag, $_SERVER['REQUEST_TIME'], $padCacheServerAge);

    if ( ! $padCacheServerNoData ) {
      apcu_store($url,  [$_SERVER['REQUEST_TIME'], $etag], $padCacheServerAge);
      $data = apcu_fetch("x$etag");
      if ( $data !== FALSE )
        apcu_store("x$etag", $data, $padCacheServerAge+10);
    }

  }

  function padCacheDelete ($url, $etag) {

    global $padCacheServerNoData;

    apcu_delete($etag);

    if ( ! $padCacheServerNoData )
      apcu_delete("x$etag");

  }

?>