<?php

  // pad pack <app>[,<app>...] [<directory>] [--name=x] [--panel=button|key|none] [--id=x] [--serve[=port]]
  // pad pack --install
  //
  // Packs applications into a directory that runs in a browser with no PHP server: PHP
  // compiled to WebAssembly, the PAD engine, the applications with their www/ files, and the
  // runtime that answers their requests (wasm/app.mjs, wasm/README.md). Opened from the disk
  // it runs in the tab; served over https - or on localhost - it installs as an application
  // that works offline. What the applications write, their SQLite databases first, is kept in
  // the browser. The directory is DATA/wasm/<app> unless one is named.
  //
  //   --serve[=port]   serves the directory on http://127.0.0.1:<port>/ (8090 unless said)
  //                    once it is packed - localhost counts as secure, so the service worker
  //                    and the install work there as they do over https
  //   --install        installs what the packing needs - the @php-wasm packages and esbuild
  //                    of wasm/package.json - with npm into ~/.cache/pad-wasm, outside the
  //                    repository, whose walkers would read a node_modules; NODE_PATH names
  //                    another place
  //
  // The rest is node's: wasm/app.mjs packs, with the options as they are given.

  $packArgs    = array_slice ( $argv, 2 );
  $packApps    = [];
  $packDir     = '';
  $packPass    = [];
  $packServe   = '';
  $packInstall = FALSE;

  foreach ( $packArgs as $packArg )
    if ( $packArg == '--install' )
      $packInstall = TRUE;
    elseif ( preg_match ( '/^--serve(=([0-9]{1,5}))?$/', $packArg, $m ) )
      $packServe = $m [2] ?? '' ?: '8090';
    elseif ( preg_match ( '/^--panel=(.*)$/', $packArg, $m ) and ! in_array ( $m [1], [ 'button', 'key', 'none' ], TRUE ) )
      return cliFail ( "--panel is button, key or none, not '{$m [1]}'" );
    elseif ( preg_match ( '/^--(name|panel|id)=.+$/s', $packArg ) )
      $packPass [] = $packArg;
    elseif ( str_starts_with ( $packArg, '--' ) )
      return cliFail ( "pad pack does not know $packArg - pad pack <app>[,<app>...] [dir] [--name=x] [--panel=button|key|none] [--id=x] [--serve[=port]], or --install" );
    elseif ( ! $packApps ) {
      $packApps = array_filter ( array_map ( 'trim', explode ( ',', $packArg ) ) );
      foreach ( $packApps as $packApp )
        if ( ! cliApp ( $packApp ) or str_starts_with ( $packApp, '_' ) )
          return cliFail ( "there is no application named '$packApp' - pad pack <app>[,<app>...] [dir]" );
    } elseif ( $packDir === '' )
      $packDir = $packArg;
    else
      return cliFail ( "pad pack takes the applications and one directory - not understood: $packArg" );

  if ( ! $packApps and ! $packInstall )
    return cliFail ( 'pad pack <app>[,<app>...] [dir] [--name=x] [--panel=button|key|none] [--id=x] [--serve[=port]], or pad pack --install' );

  $packNode = trim ( (string) shell_exec ( 'command -v node 2>/dev/null' ) );

  if ( $packNode === '' )
    return cliFail ( 'packing needs node (https://nodejs.org) on the PATH - the @php-wasm packages and esbuild run on it' );

  $packHome    = getenv ( 'HOME' ) ?: ( function_exists ( 'posix_getpwuid' ) ? ( posix_getpwuid ( posix_geteuid () ) ['dir'] ?? '' ) : '' );
  $packModules = getenv ( 'NODE_PATH' ) ?: "$packHome/.cache/pad-wasm/node_modules";

  if ( $packInstall ) {

    $packPrefix = dirname ( $packModules );

    if ( ! is_dir ( $packPrefix ) and ! mkdir ( $packPrefix, 0755, TRUE ) )
      return cliFail ( "$packPrefix could not be made" );

    copy ( cliHome () . '/wasm/package.json', "$packPrefix/package.json" );

    cliOut ( "Installing the @php-wasm packages and esbuild into $packModules" );

    passthru ( 'npm install --no-audit --no-fund --prefix ' . escapeshellarg ( $packPrefix ), $packCode );

    if ( $packCode )
      return cliFail ( 'npm could not install them' );

    if ( ! $packApps )
      return 0;

  }

  if ( ! is_dir ( "$packModules/@php-wasm/universal" ) )
    return cliFail ( "the @php-wasm packages are not in $packModules - pad pack --install puts them there" );

  if ( $packDir === '' )
    $packDir = cliHome () . '/DATA/wasm/' . implode ( '+', array_map ( fn ( $a ) => str_replace ( '/', '_', $a ), $packApps ) );

  putenv ( "NODE_PATH=$packModules" );

  $packCommand = array_merge ( [ $packNode, cliHome () . '/wasm/app.mjs', implode ( ',', $packApps ), $packDir ], $packPass );

  passthru ( implode ( ' ', array_map ( 'escapeshellarg', $packCommand ) ), $packCode );

  if ( $packCode or $packServe === '' )
    return $packCode;

  cliOut ( "Serving it on http://127.0.0.1:$packServe/ - open it there to install it; Ctrl-C stops it" );

  $packServer = [ '-S', "127.0.0.1:$packServe", '-t', $packDir ];

  if ( function_exists ( 'pcntl_exec' ) )
    pcntl_exec ( cliPhp (), $packServer );

  passthru ( escapeshellarg ( cliPhp () ) . ' ' . implode ( ' ', array_map ( 'escapeshellarg', $packServer ) ), $packCode );

  return $packCode;

?>
