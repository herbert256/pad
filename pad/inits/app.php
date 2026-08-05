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

  include PAD . 'build/build.php';

?>