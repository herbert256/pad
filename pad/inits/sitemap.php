<?php

  // A request for sitemap.xml or robots.txt that names no page of the application's own -
  // inits/page.php left the question in $padSitemapAsk - is answered here, now that the
  // configuration says whether to and $padGoExt says what the addresses look like
  // (lib/sitemap.php). Either way the request ends here: the answer, or the 404 any page
  // that is not there gets.

  if ( $padSitemapAsk !== '' )
    padSitemapAnswer ( $padSitemapAsk );

?>
