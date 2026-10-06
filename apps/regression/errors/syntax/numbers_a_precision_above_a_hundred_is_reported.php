<?php

  // A precision below PHP_INT_MAX but far past what a number holds made number_format
  // allocate that many bytes: 1e18 decimals ended the request out of memory.

  $p = 1e18;

?>
