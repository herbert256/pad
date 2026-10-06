<?php

  // Builds the four URL globals, e.g. mounted at /pad/ on localhost for app demo:
  //
  //   $padRoot   host-less mount prefix        /pad/
  //   $padHost   absolute cross-app base       http://localhost/pad/
  //   $padGo     this app, relative            /pad/demo/?   (/pad/demo/ with $padCleanUrls)
  //   $padGoExt  this app, absolute            http://localhost/pad/demo/?
  //
  // $padRoot may be preset by the entry point (www/pad.php); it defaults to /.

  // The host is the client's Host header, and the engine writes its links and redirects
  // with it and addresses its own fetches - {get}, {page app=}, SELF:// - with it; those
  // connect to this server's own socket whatever the name says (padSelfConnect, lib/paths.php),
  // so a Host naming another machine is not where the server fetches from. It must be a host
  // name (or an IP literal) with an optional port, nothing else; when $padHosts lists the
  // names this server answers to, any other is replaced by the first of them. $padHostBase,
  // when set, is the whole base and the request is not asked at all - the setting for a
  // server behind a proxy, whose scheme and port the request does not show.

  $padRequestScheme = $_SERVER ['REQUEST_SCHEME'] ?? 'http';
  $padHttpHost      = $_SERVER ['HTTP_HOST']      ?? 'localhost';
  $padServerPort    = $_SERVER ['SERVER_PORT']    ?? 80;

  if ( ( $_SERVER ['HTTPS'] ?? '' ) !== '' and ( $_SERVER ['HTTPS'] ?? '' ) !== 'off' )
    $padRequestScheme = 'https';

  if ( ! preg_match ( '/^([A-Za-z0-9]([A-Za-z0-9-]*[A-Za-z0-9])?(\.[A-Za-z0-9]([A-Za-z0-9-]*[A-Za-z0-9])?)*|\[[0-9A-Fa-f:.]+\])(:[0-9]{1,5})?$/', $padHttpHost ) )
    $padHttpHost = 'localhost';

  if ( count ( $padHosts ?? [] ) and ! in_array ( strtolower ( preg_replace ( '/:[0-9]+$/', '', $padHttpHost ) ), array_map ( 'strtolower', $padHosts ), TRUE ) )
    $padHttpHost = reset ( $padHosts );

  if (strpos ( $padHttpHost, ':') === FALSE or str_ends_with ( $padHttpHost, ']' ) )
    if ( ($padRequestScheme == 'http'  and $padServerPort != 80) or
         ($padRequestScheme == 'https' and $padServerPort != 443) )
      $padHttpHost .= ':' . $padServerPort;

  $padHost = $padRequestScheme . '://' . $padHttpHost;

  if ( ! isset ( $padRoot ) or $padRoot == DIRECTORY_SEPARATOR )
    $padRoot = '/';

  if ( ! str_ends_with ( $padRoot, '/' ) )
    $padRoot .= '/';
  $padHost .= $padRoot;

  if ( ( $padHostBase ?? '' ) !== '' )
    $padHost = rtrim ( $padHostBase, '/' ) . '/';

  // With $padCleanUrls the two write the clean form, /pad/demo/about (lib/route.php): the
  // path names the page, and a link written {$padGo}about&x=1 keeps its values, which the
  // path carries as they are.

  $padGo    = $padRoot . "$padApp/" . ( $padCleanUrls ? '' : '?' );
  $padGoExt = $padHost . "$padApp/" . ( $padCleanUrls ? '' : '?' );

  unset ( $padRequestScheme, $padHttpHost, $padServerPort );

?>
