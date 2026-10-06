<?php

  // The nonce fixture: a strict policy whose script-src allows this request's nonce, and a
  // header of the page's own that stands over the default.

  $padCsp = "default-src 'self'; script-src 'self' 'nonce'";

  header ( 'Referrer-Policy: no-referrer' );

?>
