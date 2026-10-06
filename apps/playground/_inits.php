<?php

  // The playground renders whatever template it is sent: a local request only - the command
  // line, or this machine asking itself with nothing forwarded (padLocal) - that names this
  // machine in its Host header. A page on another site that has its own name resolve to
  // 127.0.0.1 (DNS rebinding) reaches the loopback address too, and being a page of the same
  // site then, it reads the form with its CSRF token and posts its template - but it names
  // its own site as the host. Anyone else is answered 403 before a page of it runs, the way
  // inits/page.php answers a 404.

  $playHost = strtolower ( preg_replace ( '/:\d+$/', '', trim ( (string) ( $_SERVER ['HTTP_HOST'] ?? '' ) ) ) );

  if ( ! padLocal () or ( PHP_SAPI !== 'cli' and ! in_array ( $playHost, [ 'localhost', '127.0.0.1', '[::1]' ], TRUE ) ) )
    padAbort ( 403, 'The PAD playground runs the code it is given, so it answers this machine only.' );

  unset ( $playHost );

?>
