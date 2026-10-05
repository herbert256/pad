<?php

  // Pipe function after(delim): everything past the first occurrence of the delimiter -
  // after('/') on 'a/b/c' yields 'b/c'. A value that does not contain the delimiter comes
  // back unchanged, and the whole delimiter is skipped, multi-character or not - the same
  // repair afterLast() got: this used to skip exactly one character, so a longer delimiter
  // left its tail behind, and an absent one (strpos FALSE, plus one, is 1) silently ate the
  // value's first character. The work is padStrAfter in lib/str.php, which a page's PHP
  // calls too, so both cut a text the same way.

  return padStrAfter ( $value, $parm [0] );

?>