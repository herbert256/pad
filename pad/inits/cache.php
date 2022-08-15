<?php

  if ( count($_POST) or count($_FILES) or ( !$padCache_server_age and !$padCache_client_age ) ) {
    $padCache = FALSE;
    return;
  }

  if ( $app == 'pad' ) {
    $padCache = FALSE;
    return;
  }

  pTiming_start ('cache');

  $padCache        = TRUE;
  $padCache_url    = pMd5($_SERVER['REQUEST_URI']);
  $padCache_mod    = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;
  $padCache_client = isset($_SERVER['HTTP_IF_NONE_MATCH'])     ? substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, 22) : '';
  $padCache_max    = $_SERVER['REQUEST_TIME'] - $padCache_server_age;
  $padCache_age    = 0;
  $padCache_etag   = '';

  include PAD . "cache/$padCache_server_type.php";
  
  pCache_init ($padCache_url, $padCache_client);
  
  if ( $padCache_client ) {
    
    $padCache_age = pCache_etag ($padCache_client);

    if ( $padCache_age )
      $padCache_etag = $padCache_client;

    if ( $padCache_age >= $padCache_max ) {
      $padCache_stop = 304.1;
      include PAD . 'cache/stop.php';
    }
    
  }

  $url = pCache_url ($padCache_url);

  if ( is_array($url) ) {

    $padCache_age  = $url ['age']  ?? $url [0] ?? 0;
    $padCache_etag = $url ['etag'] ?? $url [1] ?? '';

    if ( $padCache_mod and $padCache_mod >= $padCache_max and $padCache_age >= $padCache_max ) {
      $padCache_stop = 304.2;
      include PAD . 'cache/stop.php';
    }

    if ( $padCache_age >= $padCache_max and ! $GLOBALS ['padCache_server_no_data'] ) {

      $padOutput = pCache_get ($padCache_etag);

      if ( $padOutput ) {
        $padCache_stop = 200.3;
        include PAD . 'cache/stop.php';
      }

    }

  }

  pTiming_end ('cache');

?>