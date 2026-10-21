<?php

  // A plain-PHP application ($padNoNo) whose root has a bracketed route, [slug].php, with the
  // sitemap and the health check on (the application nn in _nonoroute/, rendered on the
  // command line): robots.txt, sitemap.xml and up are the engine's answers, as for any
  // application, and any other name the route's page. The route's plain PHP answered all
  // four - "SLUG PAGE: robots.txt".

  // The PHP binary as the other command-line cases find it: under Apache's module PHP_BINARY
  // is empty, and the shell answered "command not found" for every name.

  $nrPhp    = ( PHP_SAPI === 'cli' or PHP_SAPI === 'cli-server' ) && PHP_BINARY !== '' ? PHP_BINARY : PHP_BINDIR . '/php';
  $nrResult = '';

  foreach ( [ 'robots.txt', 'sitemap.xml', 'up', 'other' ] as $nrAsk ) {

    $nrOut = (string) shell_exec ( escapeshellarg ( $nrPhp ) . ' ' . escapeshellarg ( "$padHome/editors/render.php" )
                                 . ' nn ' . escapeshellarg ( $nrAsk ) . ' --apps=' . escapeshellarg ( APP . '_nonoroute' ) . ' 2>&1' );

    $nrResult .= "$nrAsk: " . match ( TRUE ) {
                   str_starts_with ( $nrOut, 'User-agent:' ) => 'robots',
                   str_contains ( $nrOut, '<urlset' )         => 'sitemap',
                   str_contains ( $nrOut, '"status":"ok"' )   => 'health',
                   default                                    => trim ( $nrOut )
                 } . ' | ';

  }

?>
