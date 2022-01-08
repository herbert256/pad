<?php

  if ( count($_POST) or count($_FILES) or ( !$pad_cache_server_age and !$pad_cache_client_age ) ) {
    $pad_cache = FALSE;
    return;
  }

  pad_timing_start ('cache');

  $pad_cache        = TRUE;
  $pad_cache_url    = pad_md5($_SERVER['REQUEST_URI']);
  $pad_cache_mod    = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;
  $pad_cache_client = isset($_SERVER['HTTP_IF_NONE_MATCH'])     ? substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, 22) : '';
  $pad_cache_max    = $_SERVER['REQUEST_TIME'] - $pad_cache_server_age;
  $pad_cache_age    = 0;
  $pad_cache_etag   = '';

  include PAD . "cache/$pad_cache_server_type.php";
  
  pad_cache_init ($pad_cache_url, $pad_cache_client);
  
  if ( $pad_cache_client ) {
    
    $pad_cache_age = pad_cache_etag ($pad_cache_client);

    if ( $pad_cache_age )
      $pad_cache_etag = $pad_cache_client;

    if ( $pad_cache_age >= $pad_cache_max ) {
      $pad_cache_stop = 304.1;
      include PAD . 'cache/stop.php';
    }
    
  }

  $url = pad_cache_url ($pad_cache_url);

  if ( is_array($url) ) {

    $pad_cache_age  = $url ['age']  ?? $url [0] ?? 0;
    $pad_cache_etag = $url ['etag'] ?? $url [1] ?? '';

    if ( $pad_cache_mod and $pad_cache_mod >= $pad_cache_max and $pad_cache_age >= $pad_cache_max ) {
      $pad_cache_stop = 304.2;
      include PAD . 'cache/stop.php';
    }

    if ( $pad_cache_age >= $pad_cache_max and ! $GLOBALS['pad_cache_server_no_data'] ) {

      $pad_output = pad_cache_get ($pad_cache_etag);

      if ( $pad_output ) {
        $pad_cache_stop = 200.3;
        include PAD . 'cache/stop.php';
      }

    }

  }

  pad_timing_end ('cache');

?>