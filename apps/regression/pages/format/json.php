<?php

  // ?page&padFormat=json: the same page answers the variables it exposes as one JSON object,
  // with the JSON content type - its template does not run.

  $r = padCurl ( $padGoExt . 'format/orders&padFormat=json' );

  echo $r ['result'], ' ', $r ['headers'] ['Content-Type'] ?? '', ' ', json_encode ( json_decode ( $r ['data'] ) );

?>
