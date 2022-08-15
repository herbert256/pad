<?php

  $padRequest_scheme = $_SERVER ['REQUEST_SCHEME'] ?? 'http';
  $padHttp_host      = $_SERVER ['HTTP_HOST']      ?? 'localhost';
  $padServer_port    = $_SERVER ['SERVER_PORT']    ?? 80;
  $padScript         = $_SERVER ['SCRIPT_NAME']    ?? '/pad.php';
  $padUri            = $_SERVER ['REQUEST_URI']    ?? '/';

  if (strpos ( $padHttp_host, ':') === FALSE )
    if ( ($padRequest_scheme == 'http'  and $padServer_port <> 80) or 
         ($padRequest_scheme == 'https' and $padServer_port <> 443) )
      $padHttp_host .= ':' . $padServer_port;

  $padHost     = $padRequest_scheme . '://' . $padHttp_host;
  $padUri      = $padHost . $padScript . "?app=";
  $padGo       = $padScript . "?app=$app&page=";
  $padLocation = $padHost . $padGo;

?>