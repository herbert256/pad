<?php
  
  if ( isset($_COOKIE['cache']) and $_COOKIE['cache'] <> 'fast') {
    $pad_cache_server_age  = 3600;
    $pad_cache_server_type = $_COOKIE['cache'];
  }

  $pad_client_etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, 22) : '';

  if ( count($_POST)
    or count($_FILES)
    or (!$pad_cache_server_age and !$pad_cache_client_age or !$pad_cache_server_type)
    or ($pad_client_etag and ! preg_match('/^[a-zA-Z0-9-_]+$/', $pad_client_etag) ) ) {
    $pad_cache = $pad_cache_server_age = $pad_cache_client_age = FALSE;
    return;
  }

  pad_timing_start ('cache');

  $pad_cache      = TRUE;
  $pad_cache_url  = pad_short_md5($_SERVER['REQUEST_URI']);
  $pad_cache_max  = $_SERVER['REQUEST_TIME'] - $pad_cache_server_age;
  $pad_cache_age  = 0;
  $pad_cache_etag = '';

  include PAD_HOME . "cache/$pad_cache_server_type.php";
  
  pad_cache_init ($pad_cache_url, $pad_client_etag);
  
  if ( $pad_client_etag ) {
    
    $pad_cache_age = pad_cache_etag ($pad_client_etag);

    if ( $pad_cache_age )
      $pad_cache_etag = $pad_client_etag;

    if ( $pad_cache_age >= $pad_cache_max ) {
      pad_timing_end ('cache');
      $pad_stop = '304';
      $pad_time = $pad_cache_age;
      $pad_etag = $pad_cache_etag;
      include PAD_HOME . 'exits/stop.php';
    }
    
  }

  $url = pad_cache_url ($pad_cache_url);

  if ( is_array($url) ) {

    $pad_cache_age  = $url ['age']  ?? $url [0] ?? 0;
    $pad_cache_etag = $url ['etag'] ?? $url [1] ?? '';

    if ( $pad_cache_age >= $pad_cache_max and ! $GLOBALS['pad_cache_server_no_data']) {

      $pad_output = pad_cache_get ($pad_cache_etag);

      if ($pad_output) {
        if ( $GLOBALS['pad_cache_server_gzip'] and ! $GLOBALS['pad_client_gzip'] )
          $pad_output = pad_unzip($pad_output);
        pad_timing_end ('cache');
        $pad_stop = '200';
        $pad_time = $pad_cache_age;
        $pad_etag = $pad_cache_etag;
        include PAD_HOME . 'exits/stop.php';
      }

    }

  }

  pad_timing_end ('cache');

?>