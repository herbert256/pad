<?php

  // The same page asked twice on one page - a widget shown twice - had one id and one global
  // request: the second script took the request over while the first was in flight, both
  // answers went into the first div and the second stayed empty. The second use gets an id
  // of its own. And under a Content-Security-Policy naming 'nonce' the script carries the
  // request's nonce, as {live}'s does - without it no {ajax} loaded at all.

  $padCsp = "script-src 'nonce'";

?>
