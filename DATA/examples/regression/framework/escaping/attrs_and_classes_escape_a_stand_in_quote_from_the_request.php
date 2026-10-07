<?php

  // A quote's stand-in - U+E022, which a request can carry in a value - is escaped as the
  // quote it will become at output (exits.php restores it after all escaping), so it cannot
  // close the attribute {attrs} or {classes} builds and add an event handler.

  $t = "hi\u{E022} onmouseover=1";
  $c = "p\u{E022} onx=1";

?>
