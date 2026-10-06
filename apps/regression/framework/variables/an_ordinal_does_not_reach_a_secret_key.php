<?php

  // An ordinal names the Nth key, so a value-chosen number must not reach an engine secret by
  // its position - {$53@globals} read padSqlPassword out of $GLOBALS. A key named like a
  // secret is no answer however it is reached; the other keys resolve.

  $g = [ 'a' => 'A', 'padSqlPassword' => 'SECRET', 'c' => 'C' ];

?>
