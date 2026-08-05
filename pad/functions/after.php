<?php

  // Pipe function after(delim): everything past the first occurrence of the delimiter -
  // after('/') on 'a/b/c' yields 'b/c'. It skips exactly one character, so a multi-character
  // delimiter leaves a remainder, and an absent delimiter drops only the first character.

  return substr($value, strpos($value, $parm[0])+1);

?>