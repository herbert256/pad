<?php

  // What a visitor may have typed: the end of the script and a new one, PAD's braces and bar,
  // quotes, a backslash, Unicode and a line separator - and the stand-in of a quote, U+E022,
  // which the page's end would make a real quote inside the JSON string.

  $title = "</script><script>alert(1)</script> <!-- {php:getcwd} | {\$x} \"q\" 'q' back\\slash \u{e9} \u{4e2d} \u{1F680} \u{2028} x\u{E022}y";

?>
