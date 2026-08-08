<?php

  // This used to be trigger_error(E_USER_ERROR), which PHP 8.4 deprecates - the deprecation
  // fired before the error and was what ended the request, so the test asserted the wrong
  // failure. An engine-raised warning keeps this a PHP error reaching PAD's handler, which
  // the user-raised E_USER_WARNING of the warning tests does not cover.

  $errorTest = $errorUndefined;

?>