<?php

  // The parameter:n@tag property: the value of one positional parameter of level $padIdx,
  // or NULL when the tag has no such parameter.
  //
  // Positional parameters - the values after the tag name, as in {switch 'odd', 'even'} -
  // live in $padOpt numbered from 1; entry 0 holds the raw tag text instead. $parm is the
  // index and comes from the including function, not from a global; parameters.php returns
  // them all at once.

  global $padOpt;

  if ( isset ( $padOpt [$padIdx] [$parm] ) )
    return $padOpt [$padIdx] [$parm];
  else
    return NULL;

?>