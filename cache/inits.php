<?php

  $pad_client_etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, 22) : '';

  if ( count($_POST)
    or count($_FILES)
    or (!$pad_cache_server_age and !$pad_cache_client_age or !$pad_cache_server_type)
    or ($pad_client_etag and ! preg_match('/^[a-zA-Z0-9-_]+$/', $pad_client_etag) ) ) {
    $pad_cache = $pad_cache_client_age = $pad_cache_server_age = FALSE;
    return;
  }

  pad_timing_start ('cache');

  $pad_cache      = TRUE;
  $pad_cache_url  = pad_short_md5($_SERVER['REQUEST_URI']);
  $pad_cache_max  = $_SERVER['REQUEST_TIME'] - $pad_cache_server_age;
  $pad_cache_age  = 0;
  $pad_cache_etag = '';

  include PAD_HOME . "cache/$pad_cache_server_type.php";
  
  "pad_cache_init_$pad_cache_server_type" ($pad_cache_url, $pad_client_etag);
  
  if ( $pad_client_etag ) {
    
    $pad_cache_age = "pad_cache_etag_$pad_cache_server_type" ($pad_client_etag);

    if ( $pad_cache_age )
      $pad_cache_etag = $pad_client_etag;

    if ( $pad_cache_age >= $pad_cache_max ) {
      $pad_stop = '304';
      include PAD_HOME . 'cache/stop.php';
    }
    
  }

  $url = "pad_cache_url_$pad_cache_server_type" ($pad_cache_url);

  if ( is_array($url) ) {

    $pad_cache_age  = $url ['age']  ?? $url [0] ?? 0;
    $pad_cache_etag = $url ['etag'] ?? $url [1] ?? '';

    if ( $pad_cache_age >= $pad_cache_max and ! $GLOBALS['pad_cache_server_no_data']) {

      $pad_output = "pad_cache_get_$pad_cache_server_type" ($pad_cache_etag);

      if ($pad_output) {
        if ( $GLOBALS['pad_cache_server_gzip'] and ! $GLOBALS['pad_client_gzip'] )
          $pad_output = pad_unzip($pad_output);
         $pad_stop = '200';
        include PAD_HOME . 'cache/stop.php';
      }

    }

  }

  pad_timing_end ('cache');

?>