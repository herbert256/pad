<?php

  // Every page: the title the wrapper writes unless the page sets its own, the section of
  // the menu that is lit and the version of the files in www/alpine/ for their addresses.

  $title      = 'PAD + Alpine';
  $navSection = str_starts_with ( $padPage, 'examples/' ) ? 'index' : explode ( '/', $padPage ) [0];

  $alpineWww     = dirname ( APPS ) . "/www/$padApp/";
  $alpineVersion = max ( array_map ( 'filemtime', array_merge ( glob ( "$alpineWww*.css" ) ?: [], glob ( "$alpineWww*.js" ) ?: [],
                                                                glob ( "{$alpineWww}examples/*.js" ) ?: [] ) ) ?: [ 0 ] );

?>
