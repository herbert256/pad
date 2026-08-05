<?php

  // Range setup for the loop sequence, run before generation by sequence/inits/init.php: a
  // numeric parameter is taken as a row count, so {loop 5} yields five terms. This is why
  // the type declares flags/parm - its first parameter is a length, not a value.

  if ( is_numeric ( $pqParm ) )
    $pqRows = $pqParm;

?>