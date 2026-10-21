<?php

  // Pipe function ignore: escapes PAD's syntax characters ({ } | = , @) into &open;-style
  // placeholders so the value is never seen as tags by the parser; exits/exits.php calls
  // padUnescape to put them back when the finished page is written. This is the pipe used
  // for JSON, JavaScript or CSS embedded in the output.
  //
  // The characters are written as padProtect's stand-ins (lib/encoding.php), which the exit
  // puts back as well and which the sanitize step of a field leaves alone - {$json | ignore}
  // printed &open;"a": 1&comma; ... &close;, its entities escaped once more by that step - and
  // the braces of a content an opening pipe masked are the braces they stand for.

  if ( ! is_string ( $value ) )
    return padEscape ( $value );

  return padProtect ( str_replace ( padPipeMarks (), [ '{', '}' ], $value ) );

?>
