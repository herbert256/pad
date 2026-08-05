<?php

  // Arms the 'stats' info mode. There is nothing to measure yet - the clocks are the request
  // globals $padMicro and $padAppTime - so this only raises the flag info/types/stats/end.php
  // checks before computing anything.

  global $padInfoStatsStarted;

  $padInfoStatsStarted = TRUE;

?>