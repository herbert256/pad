<?php

  // Marker file, never included: its existence is what makes 'make' a known sequence
  // option name - see exits/done.php, exits/info/options.php and exits/store/check.php.
  //
  // make, or make='sequence|parm', is the play mode that replaces each term with whatever
  // the named sequence makes of it, rather than filtering it (keep, remove) or marking it
  // (flag); it is also the mode a run falls back to when none is named (inits/set.php).
  // inits/check/check.php reads it into $pqMake and plays/inits.php registers a valued form
  // as a play. Also usable as tag, type or prefix ({make:prime}).

  return TRUE;

?>