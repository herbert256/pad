<?php

  // A section holding the CSP nonce - a widget's or a chart's <style> under style-src 'nonce'
  // - is kept, the nonce written in on a hit as the request's: it was never kept at all.

  $padCsp = "default-src 'self'; style-src 'nonce'";

  padFragmentForget ( 'nonce-section' );

?>
