<?php

  // Pipe function right(n): the last n characters of the value, or the whole value when it
  // is shorter. A count of zero names no characters and answers the empty string - it used
  // to answer the whole value, because substr read -0 as offset 0 - and a negative count is
  // treated the same rather than being reinterpreted as an offset. Counted in characters,
  // not bytes, so a multibyte letter is never cut in half.

  $padRight = (int) $parm [0];

  if ( $padRight < 1 )
    return '';

  return mb_substr ( $value, - $padRight, NULL, 'UTF-8' );

?>