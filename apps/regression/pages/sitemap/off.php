<?php

  // With $padSitemap off - this application's setting - sitemap.xml and robots.txt are
  // pages like any other, and there are none: not found.

  echo padCurl ( $padGoExt . 'sitemap.xml' ) ['result'], ' ', padCurl ( $padGoExt . 'robots.txt' ) ['result'];

?>
