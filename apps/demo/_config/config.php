<?php

  // _inits.pad writes the whole HTML page itself, so _common's page frame stays off - with
  // it on, the demo's page was a second document inside _common's body and its own title
  // was dropped.

  $padCommon = FALSE;

  // Every form here posts back to the demo, so each carries the session's CSRF token - the
  // engine adds it to every <form method="post"> - and a post without it is turned away
  // with 403 before the page runs.

  $padCsrf = TRUE;

?>