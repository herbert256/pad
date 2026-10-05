<?php

  // Pipe function default(fallback): the value, or the fallback when it is empty - '', NULL
  // or an empty list; 0 is a value. {$title | default('Untitled')}. The same as the operator
  // form {$title | ?? 'Untitled'}; a field piped into either may be missing under the strict
  // check, as one piped into optional may.

  if ( $value === NULL or $value === '' or $value === [] )
    return $parm [0] ?? '';

  return $value;

?>
