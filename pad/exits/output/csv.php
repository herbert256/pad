<?php

  // CSV writer: the page's data answer as a table (lib/expose.php) - the first list the page
  // names in $padExpose, a header row and a line per row, made by exits/exits.php and passed
  // through the output hook - sent by the web writer with the csv content type, its own ETag
  // and a 304 when the client holds it. The file name a browser saves it under is the page's
  // as it was asked - products/42 gives 42.csv - not index.html.

  padHeader ( 'Content-Disposition: inline; filename="' . str_replace ( '"', '', padMakeSafe ( basename ( $padPageAsked ?? $padPage ), 100 ) ) . '.csv"' );

  include PAD . 'exits/output/web.php';

?>
