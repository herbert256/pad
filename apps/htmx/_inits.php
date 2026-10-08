<?php

  // Every page: the title the wrapper writes unless the page sets its own, the section of
  // the menu that is lit, the version of the files in www/htmx/ for their addresses, and the
  // header every htmx request sends - the session's CSRF token, which a post needs.

  $title      = 'PAD + htmx';
  $navSection = str_starts_with ( $padPage, 'examples/' ) ? 'index' : explode ( '/', $padPage ) [0];

  $htmxWww     = dirname ( APPS ) . "/www/$padApp/";
  $htmxVersion = max ( array_map ( 'filemtime', array_merge ( glob ( "$htmxWww*.css" ) ?: [], glob ( "$htmxWww*.js" ) ?: [] ) ) ?: [ 0 ] );

  $htmxHeaders = [ 'X-CSRF-Token' => padCsrfToken () ];

  $cartCount = htmxCart ( padSession ( 'htmxCart', [] ) ) ['count'];

?>
