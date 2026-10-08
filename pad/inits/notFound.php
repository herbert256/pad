<?php

  // The 404 of a page that is not there, when inits/page.php let it wait for the
  // configuration - the application has an error page for it, or is down for maintenance
  // and has answered 503 by now - or the sitemap or the health check is off and the
  // application has no page of that name (inits/sitemap.php, inits/health.php). The error
  // page renders on the root level, opened for it here; without one the answer is the
  // plain line (padNotFound, lib/errorPage.php).

  if ( $padNotFound !== '' ) {

    if ( padErrorPageFile ( 404 ) )
      include PAD . 'inits/level.php';

    padNotFound ( $padNotFound );

  }

?>
