<?php

  // Builds the four URL globals, e.g. mounted at /pad/ on localhost for app demo:
  //
  //   $padRoot   host-less mount prefix        /pad/
  //   $padHost   absolute cross-app base       http://localhost/pad/
  //   $padGo     this app, relative            /pad/demo/?
  //   $padGoExt  this app, absolute            http://localhost/pad/demo/?
  //
  // $padRoot may be preset by the entry point (www/pad.php); it defaults to /.

  $padRequestScheme = $_SERVER ['REQUEST_SCHEME'] ?? 'http';
  $padHttpHost      = $_SERVER ['HTTP_HOST']      ?? 'localhost';
  $padServerPort    = $_SERVER ['SERVER_PORT']    ?? 80;

  if (strpos ( $padHttpHost, ':') === FALSE )
    if ( ($padRequestScheme == 'http'  and $padServerPort != 80) or
         ($padRequestScheme == 'https' and $padServerPort != 443) )
      $padHttpHost .= ':' . $padServerPort;

  $padHost = $padRequestScheme . '://' . $padHttpHost;

  if ( ! isset ( $padRoot ) or $padRoot == DIRECTORY_SEPARATOR )
    $padRoot = '/';

  if ( ! str_ends_with ( $padRoot, '/' ) )
    $padRoot .= '/';
  $padHost .= $padRoot;

  $padGo    = $padRoot . "$padApp/?";
  $padGoExt = $padHost . "$padApp/?";

  unset ( $padRequestScheme, $padHttpHost, $padServerPort );

?>