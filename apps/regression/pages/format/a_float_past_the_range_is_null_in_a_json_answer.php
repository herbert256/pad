<?php

  // JSON has no INF or NAN: a JSON answer writes them null, as a browser's JSON.stringify
  // does, where the partial output wrote 0 - a number the page never held - while its HTML
  // and its CSV show INF. The CSV keeps INF.

  $infJson = padCurl ( $padGoExt . 'format/infinite&padFormat=json' );
  $infCsv  = padCurl ( $padGoExt . 'format/infinite&padFormat=csv' );

  echo $infJson ['result'], ' ', json_encode ( json_decode ( $infJson ['data'] ) ), ' ', str_replace ( "\n", ';', trim ( $infCsv ['data'] ) );

?>
