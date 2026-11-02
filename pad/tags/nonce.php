<?php

  // {pad:nonce}: this request's nonce, for <script nonce="{pad:nonce}"> under a Content-Security-
  // Policy whose script-src holds 'nonce' - the engine puts the same value in the header.
  // padNonce in lib/security.php. Outside an {pad:ignore}: inside one it is text.

  return padNonce ();

?>
