<?php

  // The document around every page is the frame every PAD application shares - the brand top
  // left, the menu, the bar with the title and the theme switcher - opened in _inits.pad with
  // the common: prefix ({common:padFrame}), so _common itself stays off: the demo shows
  // what an application has of its own, without _common's tags and library.

  $padCommon = FALSE;

  // Every form here posts back to the demo, so each carries the session's CSRF token - the
  // engine adds it to every <form method="post"> - and a post without it is turned away
  // with 403 before the page runs.

  $padCsrf = TRUE;

?>
