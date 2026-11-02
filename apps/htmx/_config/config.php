<?php

  // _common's parts stay off - its tags and its title from the page's name - and the page
  // is sent as the templates write it - tidy would re-indent it. The frame of the PAD
  // applications is reached all the same, {common:padFrame} in _inits.pad.
  $padCommon = FALSE;
  $padTidy   = FALSE;

  // A post without the session's token is answered 403 before the page runs. A {pad:form}
  // carries the token as a field; the hx-post of anything else sends it in the
  // X-CSRF-Token header the element around the page names with hx-headers (_inits.pad).
  $padCsrf = TRUE;

?>
