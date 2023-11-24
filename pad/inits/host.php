<?php

  $padRequestScheme = $_SERVER ['REQUEST_SCHEME'] ?? 'http';
  $padHttpHost      = $_SERVER ['HTTP_HOST']      ?? 'localhost';
  $padServerPort    = $_SERVER ['SERVER_PORT']    ?? 80;
  $padScript        = $_SERVER ['SCRIPT_NAME']    ?? '/pad.php';
  $padUri           = $_SERVER ['REQUEST_URI']    ?? '/';

  $padScript = str_replace('index.php', '', $padScript);

  if (strpos ( $padHttpHost, ':') === FALSE )
    if ( ($padRequestScheme == 'http'  and $padServerPort <> 80) or 
         ($padRequestScheme == 'https' and $padServerPort <> 443) )
      $padHttpHost .= ':' . $padServerPort;

  $padHost = $padRequestScheme . '://' . $padHttpHost;

  $padGo    = $padScript . "?";
  $padGoExt = $padHost . $padGo;

?>