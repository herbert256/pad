<?php

  // The part of this file's own path after the engine's try/ directory - the last /try/,
  // so an install path with a try directory of its own is not taken for it, and with the
  // separators made forward so a Windows path is read the same.

  $padTry = str_replace ( '\\', '/', __FILE__ );
  $padTry = substr ( $padTry, strrpos ( $padTry, '/try/' ) + 5 );

  return include PAD . 'try/try.php';

?>