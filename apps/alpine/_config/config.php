<?php

  // _common's parts stay off - its tags and its title from the page's name - and the page
  // is sent as the templates write it. The frame of the PAD applications is reached all the
  // same, {common:padFrame} in _inits.pad.
  $padCommon = FALSE;
  $padTidy   = FALSE;

  // A post without the session's token is answered 403 before the page runs: the fetch of a
  // component sends it in the X-CSRF-Token header, read from <meta name="csrf-token">.
  $padCsrf = TRUE;

?>
