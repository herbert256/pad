<?php

  // The title of a page, from its name - the bar of the frame shows it (_tags/padFrame.php),
  // and a page's own PHP or {meta title=...} sets another.

  $title = $padPage;
  $title = str_replace ( '/index', '', $title );
  $title = padExplode ( $title, '/' );
  $title = end ( $title ) ;

?>
