<?php

  // Every page: the title the wrapper writes unless the page sets its own, the section of
  // the menu that is lit, and the version of the files in www/webcomponents/ for their
  // addresses.

  $title      = 'PAD + web components';
  $navSection = str_starts_with ( $padPage, 'examples/' ) ? 'index' : explode ( '/', $padPage ) [0];

  $wcWww     = dirname ( APPS ) . "/www/$padApp/";
  $wcVersion = max ( array_map ( 'filemtime', array_merge ( glob ( "$wcWww*.css" ) ?: [], glob ( "$wcWww*.js" ) ?: [],
                                                            glob ( "{$wcWww}elements/*" ) ?: [] ) ) ?: [ 0 ] );

?>
