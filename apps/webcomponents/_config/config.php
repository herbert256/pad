<?php

  // The page stands in the frame of the PAD applications, reached with the common: prefix
  // ({common:padFrame} in _inits.pad), so the rest of _common stays off; the page is sent as
  // the templates write it.
  $padCommon = FALSE;
  $padTidy   = FALSE;

  // Posts need the session's token: a {pad:form} carries it, the rating's fetch sends it in the
  // X-CSRF-Token header, read from <meta name="csrf-token">.
  $padCsrf = TRUE;

?>
