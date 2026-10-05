<?php

  // Live reload: a page of a local request carries a small script that asks the server, once
  // a second, for the newest modification time of the files behind the application - and
  // reloads the page when it changes. Save a template, a page's PHP, a stylesheet, and the
  // browser shows it. With pad serve no other tool is needed.
  //
  // Opt-in with $padReload in _config/config.php: 'local' (or TRUE) watches the
  // application's directory, its entry point directory in www/ and _common when the
  // application uses it; 'engine' watches pad/ as well, for work on the engine itself. Like
  // the toolbar it is for this machine's own
  // requests only (padLocal), never in a &padInclude fragment, a page that is not HTML
  // answering 200, the page cache - it is added on the way out - or a pad export.
  //
  // padReloadAdd     exits/output.php: the script, with the stamp the page was rendered at -
  //                  it reloads only on an answer that is a stamp, so a server that does not
  //                  answer the poll (reload switched on by a page's PHP, too late for the
  //                  poll, which is answered before any page runs) never loops
  // padReloadAnswer  inits/reload.php: a request with padReload in it is answered with the
  //                  current stamp and nothing else
  // padReloadStamp   the newest modification time under the watched directories

  function padReloadOn () {

    global $padReload, $padInclude, $padOutputType;

    if ( ! $padReload or $padInclude or $padOutputType != 'web' )
      return FALSE;

    return padLocal ();

  }

  function padReloadAdd () {

    global $padGo, $padPage;

    if ( ! padReloadOn () )
      return;

    $url   = json_encode ( $padGo . $padPage . '&padReload', JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP );
    $stamp = json_encode ( (string) padReloadStamp (), JSON_HEX_TAG );

    padOutputAdd ( "\n<script id=\"padReload\">(function () {\n"
      . "  var url = $url, last = $stamp;\n"
      . "  function poll () {\n"
      . "    fetch ( url, { cache: 'no-store' } )\n"
      . "      .then ( function ( r ) { return r.ok ? r.text () : null; } )\n"
      . "      .then ( function ( t ) { t = ( t || '' ).trim (); if ( /^[0-9]+$/.test ( t ) && t !== last ) location.reload (); else setTimeout ( poll, 1000 ); } )\n"
      . "      .catch ( function () { setTimeout ( poll, 2000 ); } );\n"
      . "  }\n"
      . "  setTimeout ( poll, 1000 );\n"
      . "}) ();</script>\n" );

  }

  // The poll: answered before the page is looked at further than its name, with the stamp
  // as plain text and nothing cached. A request that is not local, or with reload off, is
  // an ordinary request - the parameter means nothing to it.

  function padReloadAnswer () {

    if ( ! isset ( $_GET ['padReload'] ) or ! padReloadOn () )
      return;

    while ( ob_get_level () )
      ob_end_clean ();

    if ( ! headers_sent () ) {
      header ( 'Content-Type: text/plain; charset=UTF-8' );
      header ( 'Cache-Control: no-cache, no-store' );
    }

    echo padReloadStamp ();

    $stop = 200;

    include PAD . 'exits/exit.php';

  }

  function padReloadStamp () {

    global $padReload, $padCommon, $padApp;

    $dirs = [ APP, dirname ( APPS ) . "/www/$padApp/" ];

    if ( $padCommon )
      $dirs [] = COMMON;

    if ( $padReload === 'engine' )
      $dirs [] = PAD;

    $stamp = 0;

    foreach ( $dirs as $dir )
      $stamp = max ( $stamp, padReloadNewest ( rtrim ( $dir, '/' ) ) );

    return $stamp;

  }

  // The newest modification time in a directory tree. Hidden names are passed over - an
  // editor's swap file or .DS_Store is not a change to the page - and so are the
  // directories of the applications nested in this one, which watch for themselves.

  function padReloadNewest ( $dir ) {

    $newest = (int) @filemtime ( $dir );
    $files  = @scandir ( $dir );

    if ( ! $files )
      return $newest;

    foreach ( $files as $file ) {

      if ( $file [0] == '.' )
        continue;

      $path = "$dir/$file";

      if ( is_dir ( $path ) ) {
        if ( ! is_link ( $path ) and ! file_exists ( "$path/index.php" ) )
          $newest = max ( $newest, padReloadNewest ( $path ) );
      } else
        $newest = max ( $newest, (int) @filemtime ( $path ) );

    }

    return $newest;

  }

?>
