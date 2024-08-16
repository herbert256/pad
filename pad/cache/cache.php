<?php

  include '/pad/config/cache.php';

  if ( $padOutputType <> 'web' )
    $padCache = FALSE; ;
  elseif ( isset ( $_SERVER['REQUEST_METHOD'] ) and $_SERVER['REQUEST_METHOD'] <> 'GET' )
    $padCache = FALSE; ;
  elseif ( ! $padCacheServerAge ) 
    $padCache = FALSE; ;

  if ( ! $padCache )
    return;

  $padCacheUrl = padMD5($_SERVER['REQUEST_URI']);
  $padCacheMax = $_SERVER['REQUEST_TIME'] - $padCacheServerAge;

  include_once "/pad/cache/types/$padCacheServerType.php";
  
  padCacheInit ($padCacheUrl, $padCacheClient);
  
  if ( $padCacheClient ) {
    
    $padCacheAge = padCacheEtag ($padCacheClient);

    if ( $padCacheAge and $padCacheAge >= $padCacheMax ) {
      $padStop = 304;
      $padEtag = $padCacheClient;
      include '/pad/cache/hit.php';
    }
    
  }

  $url = padCacheUrl ($padCacheUrl);

  if ( is_array($url) ) {

    $padCacheAge  = $url ['age']  ?? $url [0] ?? 0;
    $padCacheEtag = $url ['etag'] ?? $url [1] ?? '';

    if ( $padClientDate and $padClientDate >= $padCacheMax and $padCacheAge >= $padCacheMax ) {
      $padStop = 304;
      $padEtag = $padCacheEtag;
      include '/pad/cache/hit.php';    
    }

    if ( $padCacheAge >= $padCacheMax and ! $GLOBALS ['padCacheServerNoData'] ) {

      $padOutput = padCacheGet ($padCacheEtag);

      if ( $padOutput ) {
        $padStop = 200;
        $padEtag = $padCacheEtag;
        include '/pad/cache/hit.php';
      }

    }

  }

?>