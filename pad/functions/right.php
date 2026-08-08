<?php

  // Pipe function right(n): the last n characters of the value, or the whole value when it
  // is shorter. A count of zero names no characters and answers the empty string - it used
  // to answer the whole value, because substr read -0 as offset 0 - and a negative count is
  // treated the same rather than being reinterpreted as an offset.

  $padRight = (int) $parm [0];

  if ( $padRight < 1 )
    return '';

  return substr ( $value, - $padRight );

?>
