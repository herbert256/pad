<?php

  // Defaults for the sequence subsystem, loaded by inits/config.php right after
  // config/config.php: how many rows a sequence yields when the tag names no count, and how
  // many candidate values a filtered sequence may test before it gives up. Both are read by
  // sequence/inits/limits.php to fill in $pqRows and $pqTry; the time a run may take is
  // read by sequence/build/one.php.

  $padSeqDefaultRows  = 10;
  $padSeqDefaultTries = 10000;

  // The hard ceiling on candidates for a run whose natural end may never come - a stop= or
  // to= value, an explicit try=, or a position lookup such as sequence:prime(1230). Those
  // were unbounded, and a run that never reached its stop spun until the time limit.

  $padSeqMaxTries     = 1000000;

  // The same ceiling in time: a run still looking after this many seconds ends as one that
  // ran out of candidates does, with the terms it found. A million candidates is a few
  // seconds for a cheap test and minutes for a costly one, and such a run ended on PHP's
  // 30-second limit with a 500 instead of on its cap. Showing what a run found is the page's
  // own cost: a million rows take their time to render. 0 or less, INF, or anything that is
  // no finite number is no limit; a pulled store and a fixed or given list, which end with
  // their data, are never cut by time.

  $padSeqMaxSeconds   = 5;

?>
