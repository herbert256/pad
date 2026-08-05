<?php

  // Pipe function beforeLast(delim): everything up to the last occurrence of the delimiter -
  // beforeLast('/') on 'a/b/c' yields 'a/b'. Empty string when the delimiter is absent.

  return substr($value, 0, strrpos( $value, $parm[0]));

?>