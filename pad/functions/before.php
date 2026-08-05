<?php

  // Pipe function before(delim): everything up to the first occurrence of the delimiter -
  // before('/') on 'a/b/c' yields 'a'. Yields the empty string when the delimiter is absent.

  return substr($value, 0, strpos( $value, $parm[0]));

?>