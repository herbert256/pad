<?php

  if ( ( ! $padCacheServerAge and ! $padCacheClientAge ) or count($_POST) or count($_FILES) )
    return;

  $padCache       = TRUE;
  $padCacheUrl    = padMD5($_SERVER['REQUEST_URI']);
  $padCacheMod    = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;
  $padCacheClient = isset($_SERVER['HTTP_IF_NONE_MATCH'])     ? substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, 22) : '';
  $padCacheMax    = $_SERVER['REQUEST_TIME'] - $padCacheServerAge;

  include pad . "cache/types/$padCacheServerType.php";
  
  padCacheInit ($padCacheUrl, $padCacheClient);
  
  if ( $padCacheClient ) {
    
    $padCacheAge = padCacheEtag ($padCacheClient);

    if ( $padCacheAge and $padCacheAge >= $padCacheMax )
      padStop ( 304, 'cache-client', $padCacheAge, $padCacheClient );
    
  }

  $url = padCacheUrl ($padCacheUrl);

  if ( is_array($url) ) {

    $padCacheAge  = $url ['age']  ?? $url [0] ?? 0;
    $padCacheEtag = $url ['etag'] ?? $url [1] ?? '';

    if ( $padCacheMod and $padCacheMod >= $padCacheMax and $padCacheAge >= $padCacheMax ) 
      padStop ( 304, 'cache-modified', $padCacheAge, $padCacheEtag );

    if ( $padCacheAge >= $padCacheMax and ! $GLOBALS ['padCacheServerNoData'] ) {

      $padOutput = padCacheGet ($padCacheEtag);

      if ( $padOutput )
        padStop ( 200, 'cache-data', $padCacheAge, $padCacheEtag );

    }

  }

?>