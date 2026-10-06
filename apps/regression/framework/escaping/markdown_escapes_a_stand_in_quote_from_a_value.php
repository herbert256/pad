<?php

  // The markdown pipe runs on a value before it is protected, so a value from the request
  // may hold a quote's stand-in (U+E022); it is escaped as the quote it will be, in a link
  // title and in text alike, where it passed the renderer and became a live quote that
  // closed the title attribute the renderer built.

  $b = "[a](http://x \"t\u{E022} onx=1\") and \u{E022} text";

?>
