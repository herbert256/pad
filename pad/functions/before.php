<?php

  // Pipe function before(delim): everything up to the first occurrence of the delimiter -
  // before('/') on 'a/b/c' yields 'a'. A value that does not contain the delimiter comes
  // back unchanged - strpos answered FALSE and substr read that as length zero, so the
  // absent case used to yield the empty string by coercion rather than decision, and made
  // before(x) disagree with afterLast(x) over the same value.

  $padBefore = strpos ( $value, $parm [0] );

  if ( $padBefore === FALSE )
    return $value;

  return substr ( $value, 0, $padBefore );

?>