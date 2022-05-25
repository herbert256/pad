<?php

  include PAD . 'inits/app.php';
  include PAD . 'inits/page.php';

  ob_start();
 
  $pad_lib = PAD . 'lib';
  include PAD . 'inits/lib.php';

  $PADSESSID = $PADSESSID ?? $_POST['PADSESSID'] ?? $_GET['PADSESSID'] ?? $_COOKIE['PADSESSID'] ?? pad_random_string();
  $PADREFID  = $PADREFID  ?? $_POST['PADREQID']  ?? $_GET['PADREQID']  ?? $_COOKIE['PADREQID']  ?? '';
  $PADREQID  = pad_random_string();

  $pad_trace_dir_base = "trace/$app/$page/$PADREQID";
  $pad_trace_dir_lvl  = "$pad_trace_dir_base/tree";
  $pad_trace_dir_occ  = "$pad_trace_dir_base/tree";

  $pad_output     = '';
  $pad_stop       = '000';
  $pad_cache_stop = 0;
  $pad_etag       = '';
  $pad_exit       = 1;
  $pad_time       = $_SERVER['REQUEST_TIME'];  

  $pad_lvl = 1;  
  include PAD . 'inits/level.php';

  include PAD . 'config/config.php';

  if ( file_exists ( APP . 'config/config.php' ) )
    include APP . 'config/config.php';

  if ($pad_no_no) 
    include PAD . 'inits/nono.php';

  if ( isset($_SERVER['QUERY_STRING']) and $_SERVER['QUERY_STRING'] and strpos($_SERVER['QUERY_STRING'], '=') === FALSE )
    include PAD . 'inits/fast.php';

  include PAD . 'inits/error.php';
  include PAD . 'inits/trace.php';

  if ( ! headers_sent () ) {
    if ( ! isset($_COOKIE['PADSESSID']) or $_COOKIE['PADSESSID'] <> $PADSESSID )
      setCookie ('PADSESSID', $PADSESSID, time() + (60 * 60 * 24 * 366 * 10) );
    setCookie ('PADREQID', $PADREQID, time() + (60 * 60 * 24 * 366 * 10) );
    pad_header ("X-PAD-ID: $PADREQID");
  }

  if ($pad_client_gzip and (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) or strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === FALSE))
    $pad_client_gzip = FALSE;

  $pad_lib = APP . 'lib';
  include PAD . 'inits/lib.php';
  
  include PAD . 'cache/cache.php';
  include PAD . 'options/go/inits.php';
  include PAD . 'inits/parms.php';

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
  $pad          = $pad_script . "?app=$app&page=";
  $pad_location = $pad_host . $pad;

  $pad_next = $page;

  if ( isset($_REQUEST['pad_include']) )
    $pad_build_mode= 'include';

?>