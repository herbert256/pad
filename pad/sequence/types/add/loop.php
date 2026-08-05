<?php

  // Build strategy 'loop' for the add sequence: each term is the loop value plus the
  // parameter, so {add 5, from=1} gives 6, 7, 8, ... The type declares flags/parm, so the
  // tag's first parameter is the addend and not a row count. As a play, {make add=5} shifts
  // every term of another sequence by five.

  return $pqLoop + $pqParm;

?>