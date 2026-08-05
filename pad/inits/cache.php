<?php

  // Brings the caching subsystem up when the application enabled $padCache, and does nothing
  // at all otherwise - which is why config/cache.php is only ever read from cache/inits.php.
  // A cache hit can answer the request from here, before any page is rendered.

  if ( $padCache )
    include PAD . 'cache/inits.php';

?>