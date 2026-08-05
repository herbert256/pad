<?php

  // {echo <expression>} evaluates an expression and prints the result - the general
  // purpose output tag, and the one to put in front of a pipe. It evaluates $padOpt
  // [$pad] [0], the unparsed option text, rather than a parsed parameter, so the whole
  // expression reaches padEval() as it was written.

  return padEval ( $padOpt [$pad] [0] );

?>