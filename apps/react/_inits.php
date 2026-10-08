<?php

  // Every page: the title the wrapper writes unless the page sets its own, the section of
  // the menu that is lit - the first part of the page's name, the examples belonging to the
  // patterns - and the version of the files in www/react/.

  $title      = 'PAD + React';
  $navSection = explode ( '/', $padPage ) [0];

  if ( $navSection == 'examples' )
    $navSection = 'patterns';

  // The version of www/react/ - when its newest file was saved - for the addresses of the
  // stylesheet and the scripts: a browser keeps such a file a while without asking again,
  // and would run the old copy of a component that changed (pad-react.js).

  $reactWww     = dirname ( APPS ) . "/www/$padApp/";
  $reactFiles   = array_merge ( glob ( "$reactWww*.css" ) ?: [], glob ( "$reactWww*.js" ) ?: [], glob ( "{$reactWww}examples/*.js" ) ?: [] );
  $reactVersion = max ( array_map ( 'filemtime', $reactFiles ) ?: [ 0 ] );

?>
