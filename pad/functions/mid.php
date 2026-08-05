<?php

  // Pipe function mid(start, length): substring with a 1-based start position, so mid(7, 5)
  // on 'Hello World' yields 'World'. substr is the 0-based equivalent.

  return substr($value, $parm[0]-1, $parm[1]);

?>