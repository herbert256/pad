<?php

  // Pipe function before(delim): everything up to the first occurrence of the delimiter -
  // before('/') on 'a/b/c' yields 'a'. A value that does not contain the delimiter comes
  // back unchanged - strpos answered FALSE and substr read that as length zero, so the
  // absent case used to yield the empty string by coercion rather than decision, and made
  // before(x) disagree with afterLast(x) over the same value. The work is padStrBefore in
  // lib/str.php, which a page's PHP calls too.

  return padStrBefore ( $value, $parm [0] );

?>