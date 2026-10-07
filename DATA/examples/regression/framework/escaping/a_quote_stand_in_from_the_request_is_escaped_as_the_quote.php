<?php

  // A value as a request brings it: U+E022 and U+E027 are the stand-ins of the two quotes
  // and U+E05C the one of the backslash (lib/encoding.php), which exits.php turns back into
  // the characters as the page is written out - after every escaper has run.

  $name = "x\u{E022} on=\u{E027}y\u{E05C}";

?>
