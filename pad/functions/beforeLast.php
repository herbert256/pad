<?php

  // Pipe function beforeLast(delim): everything up to the last occurrence of the delimiter -
  // beforeLast('/') on 'a/b/c' yields 'a/b'. A value that does not contain the delimiter
  // comes back unchanged, the same convention as afterLast() and, since the same repair,
  // before(): strrpos answering FALSE used to read as length zero and yield '' by accident.

  $padBeforeLast = strrpos ( $value, $parm [0] );

  if ( $padBeforeLast === FALSE )
    return $value;

  return substr ( $value, 0, $padBeforeLast );

?>