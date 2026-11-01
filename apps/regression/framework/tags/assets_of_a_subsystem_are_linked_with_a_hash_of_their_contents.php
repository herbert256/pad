<?php

  // On a web page the style sheets and the scripts of the subsystems are files of
  // www/assets/<sub>/ the page links - a <link> behind the colours of the widget, a <script
  // src> - each address with ?v= and ten hex digits of a hash of the file's contents, so a
  // browser keeps the file and a changed file is a new address. Their text was written into
  // every page, the style of a tab set and the whole runtime of the games with each.

  $padAssetLinks = TRUE;

  $tabsHash = substr ( hash ( 'xxh128', file_get_contents ( dirname ( PAD ) . '/www/assets/widgets/tabs.css' ) ), 0, 10 );
  $gameHash = substr ( hash ( 'xxh128', file_get_contents ( dirname ( PAD ) . '/www/assets/games/game.js'    ) ), 0, 10 );

?>
