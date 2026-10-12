<?php

  // A value carrying the characters of a {stack} marker and the number of a stack is text:
  // the stack's scripts were written into it a second time - only a marker the tag wrote,
  // sealed with the installation's key, is filled.

  $q = "\u{E0F0}" . crc32 ( 'scripts' ) . "\u{E0F1}";

?>
