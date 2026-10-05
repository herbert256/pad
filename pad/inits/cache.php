<?php

  // Brings the caching subsystem up when the application enabled $padCache, and does nothing
  // at all otherwise - which is why config/cache.php is only ever read from cache/inits.php.
  // A cache hit can answer the request from here, before any page is rendered.

  // A page's own {meta cache=...} - its time in the cache, or 0 to stay out - is read from
  // its file first, as a hit answers before anything is built: lib/meta.php.

  padMetaCache ();

  if ( $padCache )
    include PAD . 'cache/inits.php';

?>