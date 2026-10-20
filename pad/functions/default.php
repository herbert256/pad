<?php

  // Pipe function default(fallback): the value, or the fallback when it is empty - '', NULL,
  // FALSE or an empty list (padEvalEmpty); 0 is a value. {$title | default('Untitled')}. The
  // same as the operator form {$title | ?? 'Untitled'}; a field piped into either may be
  // missing under the strict check, as one piped into optional may. FALSE was a value here,
  // (php:is_numeric('a') | default('x')) empty, where ?? took it for empty.

  if ( padEvalEmpty ( $value ) )
    return $parm [0] ?? '';

  return $value;

?>
