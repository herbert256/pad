<?php

  // Retired gallery entry points only redirect; the application and examples have one owner.
  // A relative mount is derived from the entry point, never from a supplied Host header.
  function padShowcaseDestination ( $server, $app = '', $prefix = '', $routes = [] ) {
    $script = str_replace ( '\\', '/', $server ['SCRIPT_NAME'] ?? '' );
    $base = rtrim ( dirname ( $script ), '/' );
    $mount = rtrim ( dirname ( $base ), '/' );
    $path = parse_url ( $server ['REQUEST_URI'] ?? '', PHP_URL_PATH ) ?: '';
    $path = str_starts_with ( $path, "$base/" ) ? substr ( $path, strlen ( $base ) + 1 ) : '';
    $path = preg_replace ( '#^index\.php/?#', '', $path );
    if ( $path === '' ) $path = ltrim ( $server ['PATH_INFO'] ?? '', '/' );
    [ $path, $tail ] = array_pad ( explode ( '&', $path, 2 ), 2, '' );
    $query = $server ['QUERY_STRING'] ?? '';
    [ $first, $rest ] = array_pad ( explode ( '&', $query, 2 ), 2, '' );
    if ( $first !== '' && ! str_contains ( $first, '=' ) ) {
      $page = urldecode ( $first );
      $query = $rest;
    } else $page = rawurldecode ( trim ( $path, '/' ) );
    $page = $page === '' ? 'index' : $page;
    if ( $routes ) {
      if ( ! isset ( $routes [$page] ) ) return NULL;
      [ $app, $page ] = $routes [$page];
    } else $page = $prefix . $page;
    $query = implode ( '&', array_filter ( [ $tail, $query ], fn ( $part ) => $part !== '' ) );
    $query = str_replace ( [ "\r", "\n", '#' ], [ '%0D', '%0A', '%23' ], $query );
    return "$mount/$app/?" . str_replace ( '%2F', '/', rawurlencode ( $page ) ) . ( $query === '' ? '' : "&$query" );
  }

  function padShowcaseRedirect ( $app = '', $prefix = '', $routes = [] ) {
    $destination = padShowcaseDestination ( $_SERVER, $app, $prefix, $routes );
    if ( $destination === NULL ) return;
    header ( "Location: $destination", TRUE, 308 );
    exit;
  }

?>
