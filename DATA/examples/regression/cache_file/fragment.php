<?php

  // A page whose PHP picks a response fragment - from an HX-Request header, which the page
  // cache's key does not hold - is answered fresh for such a request and its fragment is
  // never stored: the HTMX swap went into the cache under the page's address, and every
  // browser after it got the bare fragment for the whole page; or the stored whole page
  // went into the swap. Two runs in a row each see both answers right; the address is new
  // on every run.

  $url = $padHost . 'regression/cache_file/?fragmented&padInclude&fragment=' . padRandomString ();

  $swap  = padCurl ( [ 'url' => $url, 'headers' => [ 'HX-Request' => 'true' ] ] );
  $page  = padCurl ( $url );
  $swap2 = padCurl ( [ 'url' => $url, 'headers' => [ 'HX-Request' => 'true' ] ] );

  $fragmentResult = 'swap: ' . trim ( $swap ['data'] ) . ' | page: ' . trim ( $page ['data'] ) . ' | swap again: ' . trim ( $swap2 ['data'] );

?>
