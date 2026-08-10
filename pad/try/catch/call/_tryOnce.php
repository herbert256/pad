<?php

  // Catch handler for call/_tryOnce.php, the include_once of an application PHP file:
  // reports the throwable with its file and line through padErrorGo() and returns '' so the
  // caller ends up with an empty result instead of a fatal.

  return PAD . 'try/catch/call/_try.php';
  
?>