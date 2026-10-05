<?php

  // An application with $padCleanUrls on. $padGo writes the clean form; a page reached by
  // its path answers through the bracketed name; padRedirect() back to that page names it
  // the way it was asked. Only the index.php/... form is followed - every server runs it -
  // and the redirect is read, not followed, since the clean form needs the server's routing.

  $base = $padHost . 'regression/clean_urls/index.php/';

  $followed = trim ( padCurl ( $base . 'products/42?padInclude' ) ['data'] );

  $back = padCurl ( [ 'url' => $base . 'products/42?back', 'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

  $location = str_replace ( $padHost, $padRoot, $back ['headers'] ['Location'] ?? '' );
  $status   = $back ['result'];

?>
