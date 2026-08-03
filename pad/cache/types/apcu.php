<?php

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