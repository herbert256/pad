<?php

  // Pipe function afterLast(delim): everything past the last occurrence of the delimiter -
  // afterLast('/') on 'a/b/c' yields 'c'. A value that does not contain the delimiter comes
  // back unchanged.
  //
  // The delimiter used to be left on the front of the answer, which neither FUNCTIONS.md nor
  // after() agrees with. The whole delimiter is skipped here, so a multi-character one is
  // dropped whole rather than leaving its tail behind.

  $padAfterLast = strrpos ( $value, $parm [0] );

  if ( $padAfterLast === FALSE )
    return $value;

  return substr ( $value, $padAfterLast + strlen ( $parm [0] ) );

?>