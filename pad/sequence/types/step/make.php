<?php

  // Make build for step: the nth term of the arithmetic series that starts at 1 and rises by
  // the parameter, 1 + (n-1)*p, so step=4 maps 1, 2, 3, ... onto 1, 5, 9, 13, ...
  //
  // Not the strategy pqBuild() picks for a plain {sequence step=4}, which takes loop.php
  // with the increment init.php sets; this is the file a {make step=4} play runs, and
  // plays/init.php discards init.php's increment so the spacing is applied only here.

  $pqLoop = 1 + ( ($pqLoop-1) * $pqParm );

  return $pqLoop;

?>