<?php

  $pad_timings_start ['init'] = microtime(true);
 
  ob_start();

  set_include_path('');

  $pad_lib = PAD . 'lib';
  include 'lib.php';

  $app  = $app  ?? $_REQUEST['app']  ?? 'pad';
  $page = $page ?? $_REQUEST['page'] ?? 'index';

  if ( ! pad_check_page ($app, $page) )
    pad_boot_error ("Page not found");

  $page = pad_get_page ($app, $page);
  
  if ( ! defined ('APP') )
    define ( 'APP', APPS . "$app/" );

  $PADSESSID = $PADSESSID ?? $_POST['PADSESSID'] ?? $_GET['PADSESSID'] ?? $_COOKIE['PADSESSID'] ?? pad_random_string();
  $PADREFID  = $PADREFID  ?? $PADREQID ?? $_POST['PADREQID']  ?? $_GET['PADREQID']  ?? $_COOKIE['PADREQID']  ?? '';
  $PADREQID  = $PADREQID  ?? pad_random_string();

  include 'vars.php';

  include PAD . 'config/config.php';

  if ( file_exists ( APP . 'config/config.php' ) )
    include APP . 'config/config.php';

  if ($pad_no_no) 
    include 'nono.php';

  if ( isset($_SERVER['QUERY_STRING']) and $_SERVER['QUERY_STRING'] and strpos($_SERVER['QUERY_STRING'], '=') === FALSE )
    include 'fast.php';

  include 'error.php';
  include 'trace.php';

  if ( ! headers_sent () ) {
    if ( ! isset($_COOKIE['PADSESSID']) or $_COOKIE['PADSESSID'] <> $PADSESSID )
      setCookie ('PADSESSID', $PADSESSID, time() + (60 * 60 * 24 * 366 * 10) );
    setCookie ('PADREQID', $PADREQID, time() + (60 * 60 * 24 * 366 * 10) );
    pad_header ("X-PAD-ID: $PADREQID");
  }

  if ($pad_client_gzip and (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) or strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === FALSE))
    $pad_client_gzip = FALSE;
  
  include PAD . 'cache/cache.php';

  $pad_lib = APP . 'lib';
  include 'lib.php';

  include PAD . 'options/go/inits.php';

  pad_get_vars ();

  $pad_request_scheme = $_SERVER ['REQUEST_SCHEME'] ?? 'http';
  $pad_http_host      = $_SERVER ['HTTP_HOST']      ?? 'localhost';
  $pad_server_port    = $_SERVER ['SERVER_PORT']    ?? 80;
  $pad_script         = $_SERVER ['SCRIPT_NAME']    ?? '/pad.php';
  $pad_uri            = $_SERVER ['REQUEST_URI']    ?? '/';

  if (strpos ( $pad_http_host, ':') === FALSE )
    if ( ($pad_request_scheme == 'http'  and $pad_server_port <> 80) or 
         ($pad_request_scheme == 'https' and $pad_server_port <> 443) )
      $pad_http_host .= ':' . $pad_server_port;

  $pad_host     = $pad_request_scheme . '://' . $pad_http_host;
  $pad_uri      = $pad_host . $pad_script . "?app=";
  $pad_go       = $pad_script . "?app=$app&page=";
  $pad_location = $pad_host . $pad_go;

  if ( isset($_REQUEST['pad_include']) )
    $pad_build_mode= 'include';

  $pad = 1;
  $pad_between = 'start';
  include PAD . 'level/setup.php';

  include PAD . 'build/build.php';

  pad_timing_end ('init');

?>