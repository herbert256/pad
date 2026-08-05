<?php

  // Pipe function afterLast(delim): the tail starting at the last occurrence of the
  // delimiter, delimiter included - afterLast('/') on 'a/b/c' yields '/c'. A value that
  // does not contain the delimiter comes back unchanged.

  return substr ( $value, strrpos ( $value, $parm [0] ) );

?>