<?php

  // _inits.pad writes the whole page itself, so _common's frame stays off, and the page is
  // sent as the templates write it.
  $padCommon = FALSE;
  $padTidy   = FALSE;

  // Posts need the session's token: a {form} carries it, the rating's fetch sends it in the
  // X-CSRF-Token header, read from <meta name="csrf-token">.
  $padCsrf = TRUE;

?>
