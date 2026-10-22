<?php

  // A plain-PHP application ($padNoNo, the nono application) has no page up, sitemap.xml or
  // robots.txt, and asked for one answers the 404 every other page that is not there gets:
  // the three were given a stand-in page to wait for the configuration, and inits/nono.php
  // took that for a page and ended the request on a 500 boot error, "Page does not exists:
  // .../apps/nono/up.php" - an error report any visitor could make on every request.

  $nonoResult = '';

  foreach ( [ 'up', 'sitemap.xml', 'robots.txt', 'nosuch' ] as $nonoAsk )
    $nonoResult .= "$nonoAsk: " . padCurl ( $padHost . "nono/?$nonoAsk" ) ['result'] . ' ';

?>
