<?php

  // Loop build for subtract: each loop value less the parameter, so subtract=3 over
  // 1, 2, 3, ... gives -2, -1, 0, 1, ...

  // A value that is no number has no term here - a word in the list a make play is handed
  // ended the request on the arithmetic.

  if ( ! is_numeric ( $pqLoop ) )
    return FALSE;

  return $pqLoop - $pqParm;

?>
