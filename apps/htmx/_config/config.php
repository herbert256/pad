<?php

  // _inits.pad writes the whole page itself, so _common's frame stays off, and the page is
  // sent as the templates write it - tidy would re-indent it.
  $padCommon = FALSE;
  $padTidy   = FALSE;

  // A post without the session's token is answered 403 before the page runs. A {form}
  // carries the token as a field; the hx-post of anything else sends it in the
  // X-CSRF-Token header the <body> names with hx-headers (_inits.pad).
  $padCsrf = TRUE;

?>
