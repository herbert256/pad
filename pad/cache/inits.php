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

  // A reference or examples crawl exists to watch the page being built - a cache hit would
  // skip the build and record nothing - so those requests always render.

  if ( isset ( $_REQUEST ['padReference'] ) or isset ( $_REQUEST ['padExamples'] ) )
    $padCache = FALSE;

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

  // A miss leaves both empty - cache/exits.php reads them when it stores the page, and on
  // the very first request of a page there is nothing they could have been set to.

  $padCacheAge  = 0;
  $padCacheEtag = '';

  include_once PAD . "cache/types/$padCacheServerType.php";

  // The client's own ETag is $padClientEtag, read by inits/client.php; this file predated
  // that name and asked for a variable nothing sets, which ended every request of the
  // first application that ever switched a cache on.

  padCacheInit ($padCacheUrl, $padClientEtag);

  if ( $padClientEtag ) {

    $padCacheAge = padCacheEtag ($padClientEtag);

    if ( $padCacheAge and $padCacheAge >= $padCacheMax ) {
      $padStop = 304;
      $padEtag = $padClientEtag;
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