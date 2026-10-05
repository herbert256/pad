<?php

  // ?page&padFormat=csv: the first list the page exposes, a header row and a line per row,
  // quoted where a value holds a comma, with the CSV content type and the page's file name.

  $r = padCurl ( $padGoExt . 'format/orders&padFormat=csv' );

  echo $r ['result'], ' ', $r ['headers'] ['Content-Type'] ?? '', ' ', $r ['headers'] ['Content-Disposition'] ?? '', ' | ',
       str_replace ( "\n", ' | ', trim ( $r ['data'] ) );

?>
