<?php

  // The checker is the engine's own script: under a policy that asks for a nonce it carries
  // this request's - an inline script without one would not run.

  $padCsp = "script-src 'self' 'nonce'";

?>
