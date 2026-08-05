<?php

  // Pipe function range(min, max): inclusive test, true when min <= value <= max; between is
  // the exclusive form. Returns a real boolean rather than the '1'/'' of the string tests.

  return ( $value >= $parm[0] and $value <= $parm[1] );

?>