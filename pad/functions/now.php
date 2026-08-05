<?php

  // Pipe function now: the current Unix timestamp, ignoring whatever was piped in. Normally
  // chained into a formatter, as in {now | date('Y-m-d')}.

  return time();

?>