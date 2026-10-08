<?php

  // Cross-origin requests, with $padCors set (lib/cors.php): a request from a listed origin
  // gets its Access-Control-* headers here, before anything can answer it - the page cache,
  // a refusal, the page - and a preflight OPTIONS request is answered 204 at once, before
  // the CSRF check and before any of the application runs. With $padCors empty nothing
  // happens.

  if ( $padCors )
    padCors ();

?>
