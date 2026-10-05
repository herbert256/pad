<?php

  // CSV writer: the page's data answer as a table (lib/expose.php) - the first list the page
  // names in $padExpose, a header row and a line per row - sent by the web writer with the
  // csv content type, its own ETag and a 304 when the client holds it. The file name a
  // browser saves it under is the page's, not index.html.

  $padOutput = padExposeCsv ();
  $padEtag   = padMD5 ( $padOutput );

  padHeader ( 'Content-Disposition: inline; filename="' . basename ( $padPage ) . '.csv"' );

  include PAD . 'exits/output/web.php';

?>