<?php

  // The {pager} of a routed page links to the name the page was asked by, products/42 - it
  // linked to the file that answers it, products/[id], which no link reaches: a 404.

  $paged = padCurl ( $padHost . 'regression/clean_urls/index.php/products/42&paged&padInclude' );

  preg_match ( '/<a [^>]*href="([^"]*)"[^>]*>2</', $paged ['data'] ?? '', $pagerLink );

  $pagerLink = html_entity_decode ( $pagerLink [1] ?? 'no link', ENT_QUOTES );
  // Followed in the index.php/ form, which every server routes without being told.

  $pagerNext = str_replace ( 'regression/clean_urls/', 'regression/clean_urls/index.php/', ltrim ( substr ( $pagerLink, strlen ( $padRoot ) ), '/' ) );
  $pagerNext = padCurl ( $padHost . $pagerNext . '&padInclude' );
  $pagerNext = $pagerNext ['result'] == 200 ? trim ( strip_tags ( preg_replace ( '/<nav.*$/s', '', $pagerNext ['data'] ) ) ) : $pagerNext ['result'];

?>
