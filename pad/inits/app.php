<?php

  // The last step of initialisation and the hand-over to the application itself: everything
  // is in place, so build/build.php assembles the page (_lib, the _inits.pad/_exits.pad
  // wrappers around @page@, and the page's own .php and .pad) and starts rendering it.
  //
  // First defines APP2, the application directory without its trailing slash. The lookup
  // helpers walk up the page's directory chain by appending path fragments that already
  // start with a slash, so they need that form; guarded because a restart comes back here.

  if ( ! defined ( 'APP2' ) )
    define ( 'APP2', substr ( APP, 0, -1) );

  // A task in place of a page: the pad command's migrate and seed set $padTask, a function,
  // before they include the engine - no request value fills a pad* name, and none is a
  // function. It runs with the application's configuration and the _lib of its root (and of
  // _common) loaded, but no guard, no _inits.php and no page: a task is no request. What it
  // writes goes straight to the terminal, what it returns is the process's exit status.

  if ( isset ( $padTask ) and $padTask instanceof Closure ) {

    include PAD . 'build/dirs.php';
    include PAD . 'build/libs.php';

    while ( ob_get_level () )
      ob_end_clean ();

    exit ( (int) $padTask () );

  }

  // The request's own page is the one build that may answer with its data instead
  // (build/expose.php); a {page} built in a nested pass later in the request may not.

  $padExposeCheck = TRUE;

  include PAD . 'build/build.php';

?>
