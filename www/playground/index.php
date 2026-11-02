<?php

  // Old bookmarks keep their fragment through this redirect. Playground's server
  // renderer now lives in Live; its example links now open the WASM editor.
  if ( ( $_SERVER ['REQUEST_METHOD'] ?? 'GET' ) !== 'GET' and ( $_SERVER ['REQUEST_METHOD'] ?? 'GET' ) !== 'HEAD' ) {
    http_response_code ( 405 );
    header ( 'Allow: GET, HEAD' );
    exit;
  }
  header ( 'Location: ../wasm/', TRUE, 302 );
  exit;

?>
