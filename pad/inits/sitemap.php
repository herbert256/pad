<?php

  // A request for sitemap.xml or robots.txt that names no page of the application's own -
  // inits/page.php left the question in $padSitemapAsk - is answered here, now that the
  // configuration says whether to and $padGoExt says what the addresses look like
  // (lib/sitemap.php). With the sitemap off it is the 404 any page that is not there gets
  // (inits/notFound.php, the application's error page when it has one) - unless a
  // bracketed route took the name, which then stays the page.

  if ( $padSitemapAsk !== '' and $padSitemap )
    padSitemapAnswer ( $padSitemapAsk );
  elseif ( $padSitemapAsk !== '' and $padPage === $padSitemapAsk )
    $padNotFound = $padSitemapAsk;

?>
