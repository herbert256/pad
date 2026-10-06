<?php

  $title = $padPage;
  $title = str_replace ( '/index', '', $title );
  $title = padExplode ( $title, '/' );
  $title = end ( $title ) ;

  // _exits.php settles $showTitle once the page's PHP has had its say about $skipTitle, but
  // a page whose PHP returns NULL is dropped before any _exits.php runs, and the frame's
  // {showTitle} then named nothing - the manual's own NULL example answered 500.

  $showTitle = TRUE;

?>
