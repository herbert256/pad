<?php

  // Every page: the title the wrapper writes unless the page sets its own, the section of
  // the menu that is lit, and the version of the stylesheet for its address. The scripts are
  // the build's - {vite} names them by their source, so they need no version of their own.

  $title      = 'PAD + islands';
  $navSection = str_starts_with ( $padPage, 'examples/' ) ? 'index' : explode ( '/', $padPage ) [0];

  $islandsCss = filemtime ( dirname ( APPS ) . "/www/$padApp/islands.css" );

?>
