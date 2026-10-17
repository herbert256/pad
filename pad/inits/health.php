<?php

  // ?up, when the application has no page of that name - inits/page.php left the question
  // in $padHealthAsk - is the health check's (inits/_lib/health.php) with $padHealth on: the
  // answer is sent and the request ends. With it off, a page that is not there is the 404
  // any other gets (inits/notFound.php); a bracketed route that took the name stays the page.
  // The check's functions are read only for that request.

  if ( $padHealthAsk ) {

    if ( $padHealth ) {
      include_once PAD . 'inits/_lib/health.php';
      padHealthAnswer ();
    }
    elseif ( $padPage === 'up' )
      $padNotFound = 'up';

  }

?>
