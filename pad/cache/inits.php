<?php

  // Server-side page cache lookup, run at the start of a request from inits/cache.php.
  //
  // Caching applies only to plain GET web requests with a non-zero $padCacheServerAge;
  // anything else clears $padCache and returns. The request URI hashes to $padCacheUrl,
  // the backend named by $padCacheServerType is loaded from cache/types/, and its
  // padCache* functions are asked for the stored ETag and its age. A fresh entry ends the
  // request there and then through cache/hit.php - 304 when the client's own ETag or
  // If-Modified-Since is good enough, otherwise 200 with the stored body - so the page is
  // never built at all.

  global $padCacheServerNoData;

  include PAD . 'config/cache.php';

  if ( $padOutputType != 'web' )
    $padCache = FALSE;
  elseif ( isset ( $_SERVER['REQUEST_METHOD'] ) and $_SERVER['REQUEST_METHOD'] != 'GET' )
    $padCache = FALSE;
  elseif ( ! $padCacheServerAge )
    $padCache = FALSE;

  if ( ! $padCache )
    return;

  $padCacheUrl = padMD5($_SERVER['REQUEST_URI']);
  $padCacheMax = $_SERVER['REQUEST_TIME'] - $padCacheServerAge;

  include_once PAD . "cache/types/$padCacheServerType.php";

  padCacheInit ($padCacheUrl, $padCacheClient);

  if ( $padCacheClient ) {

    $padCacheAge = padCacheEtag ($padCacheClient);

    if ( $padCacheAge and $padCacheAge >= $padCacheMax ) {
      $padStop = 304;
      $padEtag = $padCacheClient;
      include PAD . 'cache/hit.php';
    }

  }

  $url = padCacheUrl ($padCacheUrl);

  if ( is_array($url) ) {

    $padCacheAge  = $url ['age']  ?? $url [0] ?? 0;
    $padCacheEtag = $url ['etag'] ?? $url [1] ?? '';

    if ( $padClientDate and $padClientDate >= $padCacheMax and $padCacheAge >= $padCacheMax ) {
      $padStop = 304;
      $padEtag = $padCacheEtag;
      include PAD . 'cache/hit.php';
    }

    if ( $padCacheAge >= $padCacheMax and ! $padCacheServerNoData ) {

      $padOutput = padCacheGet ($padCacheEtag);

      if ( $padOutput ) {
        $padStop = 200;
        $padEtag = $padCacheEtag;
        include PAD . 'cache/hit.php';
      }

    }

  }

?>