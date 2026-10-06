<?php

  // Defaults for the sequence subsystem, loaded by inits/config.php right after
  // config/config.php: how many rows a sequence yields when the tag names no count, and how
  // many candidate values a filtered sequence may test before it gives up. Both are read by
  // sequence/inits/limits.php to fill in $pqRows and $pqTry.

  $padSeqDefaultRows  = 10;
  $padSeqDefaultTries = 10000;

  // The hard ceiling on candidates for a run whose natural end may never come - a stop= or
  // to= value, an explicit try=, or a position lookup such as sequence:prime(1230). Those
  // were unbounded, and a run that never reached its stop spun until the time limit.

  $padSeqMaxTries     = 1000000;

?>
