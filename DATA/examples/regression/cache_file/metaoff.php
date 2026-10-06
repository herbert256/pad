<?php

  // A page that keeps itself out of the cache with {meta cache=0}: its body is its build
  // moment, so two fetches differ although the application caches every page.

  $stamp = hrtime ( TRUE );

?>
