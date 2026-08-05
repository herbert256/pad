<?php

  // Pipe function between(min, max): exclusive test, true when min < value < max; range is
  // the inclusive form. Returns a real boolean rather than the '1'/'' of the string tests.

  return ( $value > $parm[0] and $value < $parm[1] );

?>