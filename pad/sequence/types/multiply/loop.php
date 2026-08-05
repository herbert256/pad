<?php

  // Build strategy 'loop' for the multiply sequence: each term is the loop value times the
  // parameter, so {multiply 3} gives 3, 6, 9, 12, ... The type declares flags/parm, so the
  // tag's first parameter is the multiplier rather than a row count; as a play,
  // {make multiply=3} scales another sequence's terms.

  return $pqLoop * $pqParm;

?>