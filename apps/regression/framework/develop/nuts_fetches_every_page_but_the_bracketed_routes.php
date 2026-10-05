<?php

  // develop/?nuts puts an {ajax} stub on one page for every page of every application. A
  // bracketed route is no page a URL names, and the strict check's {ajax} refused the first
  // one with a 500 for the whole page.

  $nuts    = padCurl ( $padHost . 'develop/?nuts&padInclude' );
  $status  = $nuts ['result'];
  $bracket = str_contains ( $nuts ['data'], '[id]' ) ? 'listed' : 'left out';

?>
