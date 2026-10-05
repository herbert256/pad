<?php

  // Pipe function beforeLast(delim): everything up to the last occurrence of the delimiter -
  // beforeLast('/') on 'a/b/c' yields 'a/b'. A value that does not contain the delimiter
  // comes back unchanged, the same convention as afterLast() and, since the same repair,
  // before(): strrpos answering FALSE used to read as length zero and yield '' by accident.
  // The work is padStrBeforeLast in lib/str.php, which a page's PHP calls too.

  return padStrBeforeLast ( $value, $parm [0] );

?>