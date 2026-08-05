<?php

  // The escape hatch for applications that want plain PHP with no templating at all, enabled
  // by setting $padNoNo in the application's _config/config.php (see the 'nono' app).
  //
  // Returns immediately for normal applications. Otherwise the page's .php file is run on its
  // own and the request ends there - no .pad template, no level loop, no exits. Every pad*
  // global is unset first so the page starts from a clean namespace and cannot accidentally
  // depend on engine state.
  //
  // Note this ends the request with exit, so nothing in exits/ runs: the page is responsible
  // for its own output and headers.

  if ( ! $padNoNo )
    return;

  $padNoNo = APP . "$padPage.php";

  if ( ! file_exists ( $padNoNo ) )
    padBootError ( "Page does not exists: $padNoNo" );

  foreach ( $GLOBALS as $key => $value )
    if ( substr ( $key, 0, 3 ) == 'pad' and $key != 'padNoNo' )
      unset ( $GLOBALS[$key] );

  unset ( $key );
  unset ( $value );

  include $padNoNo;

  exit;

?>