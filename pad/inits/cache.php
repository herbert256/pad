<?php

  if ( count($_POST) or count($_FILES) or ( !$pCache_server_age and !$pCache_client_age ) ) {
    $pCache = FALSE;
    return;
  }

  if ( $app == 'pad' ) {
    $pCache = FALSE;
    return;
  }

  pTiming_start ('cache');

  $pCache        = TRUE;
  $pCache_url    = pMd5($_SERVER['REQUEST_URI']);
  $pCache_mod    = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;
  $pCache_client = isset($_SERVER['HTTP_IF_NONE_MATCH'])     ? substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, 22) : '';
  $pCache_max    = $_SERVER['REQUEST_TIME'] - $pCache_server_age;
  $pCache_age    = 0;
  $pCache_etag   = '';

  include PAD . "cache/$pCache_server_type.php";
  
  pCache_init ($pCache_url, $pCache_client);
  
  if ( $pCache_client ) {
    
    $pCache_age = pCache_etag ($pCache_client);

    if ( $pCache_age )
      $pCache_etag = $pCache_client;

    if ( $pCache_age >= $pCache_max ) {
      $pCache_stop = 304.1;
      include PAD . 'cache/stop.php';
    }
    
  }

  $url = pCache_url ($pCache_url);

  if ( is_array($url) ) {

    $pCache_age  = $url ['age']  ?? $url [0] ?? 0;
    $pCache_etag = $url ['etag'] ?? $url [1] ?? '';

    if ( $pCache_mod and $pCache_mod >= $pCache_max and $pCache_age >= $pCache_max ) {
      $pCache_stop = 304.2;
      include PAD . 'cache/stop.php';
    }

    if ( $pCache_age >= $pCache_max and ! $GLOBALS['pCache_server_no_data'] ) {

      $pOutput = pCache_get ($pCache_etag);

      if ( $pOutput ) {
        $pCache_stop = 200.3;
        include PAD . 'cache/stop.php';
      }

    }

  }

  pTiming_end ('cache');

?>