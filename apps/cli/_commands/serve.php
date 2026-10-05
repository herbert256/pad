<?php

  // pad serve [port] [host] [--mount=name] [--workers=n]: PHP's built-in web server over
  // www/, no Apache needed. www/pad.php derives the mount prefix from SCRIPT_NAME, so served
  // at the root every application is at http://host:port/<app>/. --mount=pad serves it under
  // /pad/ instead, the way the Apache setup mounts it: a docroot under DATA/serve/ holds one
  // symlink, pad -> www. Several workers by default, so a page can fetch another page of the
  // same server ({get}, the regression runner) while it is being served.
  //
  // The server replaces this process where PHP can do that (pcntl), so Ctrl-C - or a kill -
  // stops the server itself rather than leaving it running behind a parent that went away.

  $serveArgs  = array_slice ( $argv, 2 );
  $servePort  = '8000';
  $serveHost  = '127.0.0.1';
  $serveMount = '';
  $serveWork  = '4';
  $serveSeen  = 0;

  foreach ( $serveArgs as $serveArg )
    if     ( preg_match ( '/^--mount=([A-Za-z0-9_-]+)$/', $serveArg, $m ) ) $serveMount = $m [1];
    elseif ( preg_match ( '/^--workers=([0-9]{1,2})$/',    $serveArg, $m ) ) $serveWork  = $m [1];
    elseif ( $serveSeen == 0 and preg_match ( '/^[0-9]{1,5}$/', $serveArg ) and ++$serveSeen ) $servePort = $serveArg;
    elseif ( $serveSeen <= 1 and preg_match ( '/^[A-Za-z0-9.:\[\]-]+$/', $serveArg ) and $serveSeen = 2 ) $serveHost = $serveArg;
    else
      return cliFail ( "pad serve [port] [host] [--mount=name] [--workers=n] - not understood: $serveArg" );

  $serveRoot = cliHome () . '/www';

  if ( $serveMount !== '' ) {

    $serveDocs = cliHome () . "/DATA/serve/$servePort";

    if ( ! is_dir ( $serveDocs ) )
      mkdir ( $serveDocs, 0755, TRUE );

    if ( ! is_link ( "$serveDocs/$serveMount" ) )
      symlink ( $serveRoot, "$serveDocs/$serveMount" );

    $serveRoot = $serveDocs;

  }

  $serveUrl = "http://$serveHost:$servePort/" . ( $serveMount !== '' ? "$serveMount/" : '' );

  cliOut ( "PAD serves {$serveUrl}<app>/ - for instance {$serveUrl}demo/ - Ctrl-C stops it" );

  putenv ( "PHP_CLI_SERVER_WORKERS=$serveWork" );

  $serveCommand = [ '-S', "$serveHost:$servePort", '-t', $serveRoot ];

  if ( function_exists ( 'pcntl_exec' ) )
    pcntl_exec ( cliPhp (), $serveCommand );

  passthru ( escapeshellarg ( cliPhp () ) . ' ' . implode ( ' ', array_map ( 'escapeshellarg', $serveCommand ) ), $serveCode );

  return $serveCode;

?>
