<?php

  // The error pages fixture: _common off, the error pages of _errors/ on (the default), the
  // CSRF check on - a post without its token is a 403 - and diagnostics off, so every
  // request is a visitor's: the 500 of a failing page is the application's page too, which
  // this machine's own requests never get.

  $padCommon      = FALSE;
  $padErrorPages  = TRUE;
  $padCsrf        = TRUE;
  $padDiagnostics = FALSE;

?>
