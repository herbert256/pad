<?php

  $pad_boot = microtime(true);

  $app  = $_REQUEST['app']  ?? 'pad';
  $page = $_REQUEST['page'] ?? 'index';

  if ( ! preg_match ( '/^[A-Za-z0-9_]+$/',   $app  ) ) pad_boot_error ("Invalid name for app: $app");
  if ( ! preg_match ( '/^[A-Za-z0-9\/_]+$/', $page ) ) pad_boot_error ("Invalid name for page: $page");

  $pad_lib = PAD_HOME . 'lib';
  include PAD_HOME . 'inits/lib.php';

  $PADSESSID = $_GET['PADSESSID'] ?? $_COOKIE['PADSESSID'] ?? pad_random_string(16);
  $PADREFID  = $_GET['PADREQID']  ?? $_COOKIE['PADREQID']  ?? '';
  $PADREQID  = pad_random_string(16);

  $pad_output  = '';
  $pad_stop    = '500';
  $pad_etag    = '';
  $pad_exit    = 1;
  $pad_time    = $_SERVER['REQUEST_TIME'];

  include PAD_HOME . 'config/config.php';

  if ( file_exists(PAD_APPS . $app . "/config/config.php") )
    include PAD_APPS . $app . "/config/config.php";

  if ($pad_no_no) {
    include PAD_APPS . $app . "/pages/$page.php";
    exit();
  }

  define ( 'PAD_APP', PAD_APPS . $app . '/' );

  include PAD_HOME . 'inits/error.php';  

  $pad_lib = PAD_APP . 'lib';
  include PAD_HOME . 'inits/lib.php';
 
  if ( isset($_SERVER['QUERY_STRING']) and $_SERVER['QUERY_STRING'] and strpos($_SERVER['QUERY_STRING'], '=') === FALSE )
    include PAD_HOME . 'inits/fast.php';

  $pad_trace_file = "trace/$app/$page/$PADREQID.txt";
  pad_trace ("pad/start", "app=$app page=$page session=$PADSESSID request=$PADREQID", TRUE);

  if ( ! isset($_COOKIE['PADSESSID']) or $_COOKIE['PADSESSID'] <> $PADSESSID )
    setCookie ('PADSESSID', $PADSESSID, time() + $pad_cookie_time);

  setCookie ('PADREQID', $PADREQID, time() + $pad_cookie_time);
  pad_header ("X-PAD: $PADREQID");

  if ($pad_client_gzip and (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) or strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') === FALSE))
    $pad_client_gzip = FALSE;

  if ($pad_track_db_request)
    $pad_track_db_session = TRUE;

  include PAD_HOME . 'cache/inits.php';

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

  $pad_occur   [0] = 0;
  $pad_current [0] = [];

  $pad_eval_cnt = $pad_fld_cnt = $pad_lvl_cnt = $pad_trc_cnt = $pad_occur_cnt = 0;

  $pad_timings = $pad_timings_start = [];

  $pad_parms_end   = ['toFlag', 'toContent', 'toData'];
  $pad_parms_start = ['random', 'start', 'end', 'dedup', 'page', 'rows', 'top', 'bottom', 'row', 'sort', 'ignore', 'source'];

  $pad_lvl  = 1;  
  $pad_next = $page;

?>