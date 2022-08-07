<?php

  $pRequest_scheme = $_SERVER ['REQUEST_SCHEME'] ?? 'http';
  $pHttp_host      = $_SERVER ['HTTP_HOST']      ?? 'localhost';
  $pServer_port    = $_SERVER ['SERVER_PORT']    ?? 80;
  $pScript         = $_SERVER ['SCRIPT_NAME']    ?? '/pad.php';
  $pUri            = $_SERVER ['REQUEST_URI']    ?? '/';

  if (strpos ( $pHttp_host, ':') === FALSE )
    if ( ($pRequest_scheme == 'http'  and $pServer_port <> 80) or 
         ($pRequest_scheme == 'https' and $pServer_port <> 443) )
      $pHttp_host .= ':' . $pServer_port;

  $pHost     = $pRequest_scheme . '://' . $pHttp_host;
  $pUri      = $pHost . $pScript . "?app=";
  $pGo       = $pScript . "?app=$app&page=";
  $pLocation = $pHost . $pGo;

?>