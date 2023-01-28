<?php

  if ( !$padCacheServerAge and !$padCacheClientAge ) 
    return;

  if ( count($_POST) or count($_FILES) )
    return;

  if ( $app == 'pad' )
    return;

  padTimingStart ('cache');

  $padCache       = TRUE;
  $padCacheUrl    = padMD5($_SERVER['REQUEST_URI']);
  $padCacheMod    = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;
  $padCacheClient = isset($_SERVER['HTTP_IF_NONE_MATCH'])     ? substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, 22) : '';
  $padCacheMax    = $_SERVER['REQUEST_TIME'] - $padCacheServerAge;
  $padCacheAge    = 0;
  $padCacheEtag   = '';

  include PAD . "pad/cache/$padCacheServerType.php";
  
  padCacheInit ($padCacheUrl, $padCacheClient);
  
  if ( $padCacheClient ) {
    
    $padCacheAge = padCacheEtag ($padCacheClient);

    if ( $padCacheAge )
      $padCacheEtag = $padCacheClient;

    if ( $padCacheAge >= $padCacheMax ) {
      $padCacheStop = 304.1;
      include PAD . 'pad/cache/stop.php';
    }
    
  }

  $url = padCacheUrl ($padCacheUrl);

  if ( is_array($url) ) {

    $padCacheAge  = $url ['age']  ?? $url [0] ?? 0;
    $padCacheEtag = $url ['etag'] ?? $url [1] ?? '';

    if ( $padCacheMod and $padCacheMod >= $padCacheMax and $padCacheAge >= $padCacheMax ) {
      $padCacheStop = 304.2;
      include PAD . 'pad/cache/stop.php';
    }

    if ( $padCacheAge >= $padCacheMax and ! $GLOBALS ['padCacheServerNoData'] ) {

      $padOutput = padCacheGet ($padCacheEtag);

      if ( $padOutput ) {
        $padCacheStop = 200.3;
        include PAD . 'pad/cache/stop.php';
      }

    }

  }

  padTimingEnd ('cache');

?>