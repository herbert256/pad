<?php

  // The {widget:breadcrumb/} of a routed page links the page to itself by the name it was asked
  // by, every segment encoded: asked as products/a%23b%26c%20d it binds a#b&c d, and its
  // link wrote that as it stands - products/a#b&amp;c d - which a browser follows to
  // products/a with a fragment. Asked in the index.php/ form, which every server routes
  // without being told.

  $crumbsPage = padCurl ( $padHost . 'regression/clean_urls/index.php/products/a%23b%26c%20d&crumbs&padInclude' );

  preg_match ( '/<a [^>]*href="([^"]*)" aria-current="page"/', $crumbsPage ['data'] ?? '', $crumbsLink );

  $crumbsLink = html_entity_decode ( $crumbsLink [1] ?? 'no link', ENT_QUOTES );

  // Followed, the link reaches the page it names, with the segment it was asked by.

  $crumbsNext = str_replace ( 'regression/clean_urls/', 'regression/clean_urls/index.php/', ltrim ( substr ( $crumbsLink, strlen ( $padRoot ) ), '/' ) );
  $crumbsNext = padCurl ( $padHost . $crumbsNext . '&padInclude' );
  $crumbsNext = $crumbsNext ['result'] == 200 ? trim ( $crumbsNext ['data'] ) : $crumbsNext ['result'];

?>
