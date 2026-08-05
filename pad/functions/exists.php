<?php

  // Pipe function exists: treats the piped value as a path relative to the application
  // directory (APP) and returns the string '1' or '0' - a file test, not a value test.

  $file_exists = APP  . $value;

  return ( file_exists ($file_exists) ) ? '1' : '0';

?>