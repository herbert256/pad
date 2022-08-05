<?php

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

?>