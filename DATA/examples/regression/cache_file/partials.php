<?php

  // The page cache answers a script's request for a part of a page fresh - X-Requested-With,
  // Turbo-Frame, X-Up-Target as well as HTMX's HX-Request - since the page's PHP may answer
  // it with one fragment: only HX-Request was recognised, and after a browser's visit had
  // stored the whole page every jQuery, Turbo or Unpoly request got that whole page. The
  // address is new on every run.

  $url = $padHost . 'regression/cache_file/?partial&padInclude&partials=' . padRandomString ();

  $page = padCurl ( $url );

  $partialsResult = 'page: ' . trim ( $page ['data'] );

  foreach ( [ 'X-Requested-With' => 'XMLHttpRequest', 'Turbo-Frame' => 'part', 'X-Up-Target' => '.part' ] as $header => $value )
    $partialsResult .= " | $header: " . trim ( padCurl ( [ 'url' => $url, 'headers' => [ $header => $value ] ] ) ['data'] );

?>
