<?php

  // An engine-raised warning - reading an undefined variable - keeps this a PHP error
  // reaching PAD's handler, which the user-raised E_USER_WARNING of the warning tests does
  // not cover. Not trigger_error(E_USER_ERROR): PHP 8.4 deprecates that, and the
  // deprecation would end the request before the error, asserting the wrong failure.

  $errorTest = $errorUndefined;

?>