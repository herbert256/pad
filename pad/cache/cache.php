<?php

  $padCacheClientEtag = isset($_SERVER['HTTP_IF_NONE_MATCH'])     ? substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, 22) : '';
  $padCacheClientDate = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;

  $padCache = FALSE;

  if ( ! $padCacheServerAge            ) return; 
  if ( count($_POST) or count($_FILES) ) return;

  $padCache    = TRUE;
  $padCacheUrl = padMD5($_SERVER['REQUEST_URI']);
  $padCacheMax = $_SERVER['REQUEST_TIME'] - $padCacheServerAge;

  include pad . "cache/types/$padCacheServerType.php";
  
  padCacheInit ($padCacheUrl, $padCacheClient);
  
  if ( $padCacheClient ) {
    
    $padCacheAge = padCacheEtag ($padCacheClient);

    if ( $padCacheAge and $padCacheAge >= $padCacheMax ) {
      $padStop = 304;
      $padEtag = $padCacheClient;
      include pad . 'cache/hit.php';
    }
    
  }

  $url = padCacheUrl ($padCacheUrl);

  if ( is_array($url) ) {

    $padCacheAge  = $url ['age']  ?? $url [0] ?? 0;
    $padCacheEtag = $url ['etag'] ?? $url [1] ?? '';

    if ( $padCacheClientDate and $padCacheClientDate >= $padCacheMax and $padCacheAge >= $padCacheMax ) {
      $padStop = 304;
      $padEtag = $padCacheEtag;
      include pad . 'cache/hit.php';    
    }

    if ( $padCacheAge >= $padCacheMax and ! $GLOBALS ['padCacheServerNoData'] ) {

      $padOutput = padCacheGet ($padCacheEtag);

      if ( $padOutput ) {
        $padStop = 200;
        $padEtag = $padCacheEtag;
        include pad . 'cache/hit.php';
      }

    }

  }

?>