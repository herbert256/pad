<?php

  // Pipe function after(delim): everything past the first occurrence of the delimiter -
  // after('/') on 'a/b/c' yields 'b/c'. A value that does not contain the delimiter comes
  // back unchanged, and the whole delimiter is skipped, multi-character or not - the same
  // repair afterLast() got: this used to skip exactly one character, so a longer delimiter
  // left its tail behind, and an absent one (strpos FALSE, plus one, is 1) silently ate the
  // value's first character.

  $padAfter = strpos ( $value, $parm [0] );

  if ( $padAfter === FALSE )
    return $value;

  return substr ( $value, $padAfter + strlen ( $parm [0] ) );

?>