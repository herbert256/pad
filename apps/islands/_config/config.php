<?php

  // The page stands in the frame of the PAD applications, reached with the common: prefix
  // ({common:padFrame} in _inits.pad), so the rest of _common stays off; the page is sent as
  // the templates write it.
  $padCommon = FALSE;
  $padTidy   = FALSE;

  // A post without the session's token is answered 403 before the page runs: post() of
  // pad-islands.js sends it in the X-CSRF-Token header, read from <meta name="csrf-token">.
  $padCsrf = TRUE;

  // The build of _frontend/ - www/islands/build/, the default - is what {vite} links; while
  // npm run dev runs, its address in build/hot makes {vite} ask the dev server instead.
  $padViteBuild = 'build';

?>
