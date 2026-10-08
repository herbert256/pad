<?php

  // _inits.pad writes the whole page itself, so _common's frame stays off, and the page is
  // sent as the templates write it.
  $padCommon = FALSE;
  $padTidy   = FALSE;

  // A post without the session's token is answered 403 before the page runs: the fetch of a
  // component sends it in the X-CSRF-Token header, read from <meta name="csrf-token">.
  $padCsrf = TRUE;

?>
