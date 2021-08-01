<?php

  $app  = $_REQUEST['app']  ?? 'pad';
  $page = $_REQUEST['page'] ?? 'index';

  if ( ! preg_match ( '/^[A-Za-z0-9\/_-]+$/', $page ) ) pad_boot_error ("Invalid page name: $page");
  if ( strpos($page, '//') !== FALSE)                   pad_boot_error ("Invalid page name '$page'");
  if ( substr($page, 0, 1) == '/')                      pad_boot_error ("Invalid page name '$page'");
  if ( substr($page, -1) == '/')                        pad_boot_error ("Invalid page name '$page'");
  
  if ( ! preg_match ( '/^[A-Za-z0-9_]+$/',   $app  ) )  pad_boot_error ("Invalid name for app: $app");
  if ( ! file_exists ( PAD_APPS . $app )             )  pad_boot_error ("Applicaton does not exists: $app");
  if ( ! is_dir ( PAD_APPS . $app )                  )  pad_boot_error ("Applicaton does not exists: $app");

  define ( 'PAD_APP', PAD_APPS . $app . '/' );
 
  ob_start();

  $pad_lib = PAD_HOME . 'lib';
  include PAD_HOME . 'inits/lib.php';

  $PADSESSID = $PADSESSID ?? $_GET['PADSESSID'] ?? $_COOKIE['PADSESSID'] ?? pad_random_string(16);
  $PADREFID  = $PADREFID  ?? $_GET['PADREQID']  ?? $_COOKIE['PADREQID']  ?? '';
  $PADREQID  = pad_random_string(16);

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
  include PAD_HOME . 'inits/level.php';

  include PAD_HOME . 'config/config.php';

  if ( file_exists(PAD_APPS . $app . "/config/config.php") )
    include PAD_APPS . $app . "/config/config.php";

  if ($pad_no_no) 
    include PAD_HOME . 'inits/nono.php';

  if ( isset($_SERVER['QUERY_STRING']) and $_SERVER['QUERY_STRING'] and strpos($_SERVER['QUERY_STRING'], '=') === FALSE )
    include PAD_HOME . 'inits/fast.php';

  include PAD_HOME . 'inits/error.php';
  include PAD_HOME . 'inits/trace.php';

  if ( ! headers_sent () ) {
    if ( ! isset($_COOKIE['PADSESSID']) or $_COOKIE['PADSESSID'] <> $PADSESSID )
      setCookie ('PADSESSID', $PADSESSID, time() + (60 * 60 * 24 * 366 * 10) );
    setCookie ('PADREQID', $PADREQID, time() + (60 * 60 * 24 * 366 * 10) );
    pad_header ("X-PAD-ID: $PADREQID");
  }

  if ($pad_client_gzip and (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) or strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === FALSE))
    $pad_client_gzip = FALSE;

  $pad_lib = PAD_APP . 'lib';
  include PAD_HOME . 'inits/lib.php';
  
  include PAD_HOME . 'cache/cache.php';
  include PAD_HOME . 'options/go/inits.php';

  pad_get_vars ();

  $pad_request_scheme = $_SERVER ['REQUEST_SCHEME'] ?? 'http';
  $pad_http_host      = $_SERVER ['HTTP_HOST']      ?? 'localhost';
  $pad_server_port    = $_SERVER ['SERVER_PORT']    ?? 80;
  $pad_script         = $_SERVER ['SCRIPT_NAME']    ?? '/pad.php';
  $pad_uri            = $_SERVER ['REQUEST_URI']    ?? '/';

  if (strpos ( $pad_http_host, ':') === FALSE )
    if ( !($pad_request_scheme == 'http'  and $pad_server_port == 80) and 
         !($pad_request_scheme == 'https' and $pad_server_port == 443) )
      $pad_http_host .= ':' . $pad_server_port;

  $pad_host     = $pad_request_scheme . '://' . $pad_http_host;
  $pad          = $pad_script . "?app=$app&page=";
  $pad_location = $pad_host . $pad;

  $pad_next = $page;

  if ( isset($_REQUEST['pad_include']) )
    $pad_build_mode= 'include';

?>