<?php

  // Pipe function max_len(n): truncates the value to at most n characters and leaves shorter
  // values untouched. A plain cut - no ellipsis, no respect for word boundaries.

  if (strlen($value) > $parm[0])
    return substr($value, 0, $parm[0]);
  else
    return $value;

?>