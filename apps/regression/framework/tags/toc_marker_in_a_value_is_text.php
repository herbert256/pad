<?php

  // A value carrying the characters of the {toc} marker is text: an odd run of hex failed
  // the page with a 500, an empty one with null + array, and a well-formed one planted a
  // table of contents in a page without a {toc}. Only a marker the tag wrote - sealed with
  // the installation's key - is filled.

  $odd    = "\u{E0F6}35\u{E0F7}";
  $forged = "\u{E0F6}" . str_repeat ( '0', 16 ) . bin2hex ( json_encode ( [ [ 2 ], 'Planted', 'ol' ] ) ) . "\u{E0F7}";

?>
