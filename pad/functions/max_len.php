<?php

  // Pipe function max_len(n): truncates the value to at most n characters and leaves shorter
  // values untouched. A plain cut - no ellipsis, no respect for word boundaries - counted in
  // characters, so a multibyte letter is never cut in half.

  if (mb_strlen($value, 'UTF-8') > $parm[0])
    return mb_substr($value, 0, $parm[0], 'UTF-8');
  else
    return $value;

?>