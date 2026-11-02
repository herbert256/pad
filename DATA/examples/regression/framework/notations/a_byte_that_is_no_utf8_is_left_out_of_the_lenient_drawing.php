<?php

  // The lenient walk: a byte that is no UTF-8 - "ab" with a Latin-1 byte between - is left
  // out of the drawing as a character with no sign, where morse, semaphore and signals ended
  // the page with a foreach() warning and braille with a TypeError.

  $padCheckSyntax = FALSE;

  $word = "a\xFFb";

?>
