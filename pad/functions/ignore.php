<?php

  // Pipe function ignore: escapes PAD's syntax characters ({ } | = , @) into &open;-style
  // placeholders so the value is never seen as tags by the parser; exits/exits.php calls
  // padUnescape to put them back when the finished page is written. This is the pipe used
  // for JSON, JavaScript or CSS embedded in the output.

   return padEscape ( $value );

?>