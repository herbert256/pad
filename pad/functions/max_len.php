<?php

  // Pipe function max_len(n): truncates the value to at most n characters and leaves shorter
  // values untouched. A plain cut - no ellipsis, no respect for word boundaries - counted in
  // characters, so a multibyte letter is never cut in half. The count is read as a number,
  // as substr's are: an empty one was a TypeError out of mb_substr.

  $padMaxLen = (int) $parm [0];

  if ( mb_strlen ( $value, 'UTF-8' ) > $padMaxLen )
    return mb_substr ( $value, 0, $padMaxLen, 'UTF-8' );
  else
    return $value;

?>
