<?php

  // Pipe function now: the current Unix timestamp, ignoring whatever was piped in. Normally
  // chained into a formatter, as in {now | date('Y-m-d')}. It is padNow's (lib/date.php),
  // so a clock a test froze with padNowFreeze is the one it reads.

  return padNow ()->getTimestamp ();

?>
