<?php

  // The sitemap this application answers: every page from the file tree - the index of a
  // directory as the directory - but not thanks ($padSitemapSkip), not admin/ (it has a
  // _guard.php), not send and back (actions: no template, they only redirect) and not
  // products/[id] (a route has no one address); and the robots.txt that points to it. The
  // dates are the files' own, so they are shown as their shape.

  $xml    = padCurl ( $padHost . 'regression/sitemap/?sitemap.xml' );
  $robots = padCurl ( $padHost . 'regression/sitemap/?robots.txt' );

  $sitemapType = $xml ['headers'] ['Content-Type'] ?? '';
  $sitemapXml  = str_replace ( $padHost, 'HOST/', preg_replace ( '/\d{4}-\d\d-\d\d/', 'YYYY-MM-DD', $xml ['data'] ) );
  $robotsType  = $robots ['headers'] ['Content-Type'] ?? '';
  $robotsTxt   = str_replace ( $padHost, 'HOST/', $robots ['data'] );

?>
