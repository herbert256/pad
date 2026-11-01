<?php

  // Under a policy whose style-src and script-src ask for a nonce, a <link> to a style sheet
  // of www/assets/ and a <script src> carry this request's nonce, as the <style> and the
  // <script> written in did - the nonce, not 'self', lets them load. The runtime of the
  // sounds is a <script src> and the recipes a line of their own behind it, with the nonce.

  $padAssetLinks = TRUE;
  $padCsp        = "default-src 'self'; script-src 'nonce'; style-src 'nonce'";

?>
