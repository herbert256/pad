<?php

  // A plain-PHP application ($padNoNo) whose root has a bracketed route, [slug].php, with the
  // sitemap and the health check on (the application nn in _nonoroute/, rendered on the
  // command line): robots.txt, sitemap.xml and up are the engine's answers, as for any
  // application, and any other name the route's page. The route's plain PHP answered all
  // four - "SLUG PAGE: robots.txt".

  $nrResult = '';

  foreach ( [ 'robots.txt', 'sitemap.xml', 'up', 'other' ] as $nrAsk ) {

    $nrOut = (string) shell_exec ( escapeshellarg ( PHP_BINARY ) . ' ' . escapeshellarg ( "$padHome/editors/render.php" )
                                 . ' nn ' . escapeshellarg ( $nrAsk ) . ' --apps=' . escapeshellarg ( APP . '_nonoroute' ) . ' 2>&1' );

    $nrResult .= "$nrAsk: " . match ( TRUE ) {
                   str_starts_with ( $nrOut, 'User-agent:' ) => 'robots',
                   str_contains ( $nrOut, '<urlset' )         => 'sitemap',
                   str_contains ( $nrOut, '"status":"ok"' )   => 'health',
                   default                                    => trim ( $nrOut )
                 } . ' | ';

  }

?>
