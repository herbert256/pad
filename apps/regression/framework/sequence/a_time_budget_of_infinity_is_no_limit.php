<?php

  // A time budget that switches the limit off by its size - INF, 1e10 - lets every run go
  // to its other ends: the deadline was the budget in nanoseconds cast to an integer, and
  // INF or 1e10 seconds is none, so every sequence tag of the application ended the
  // request on "The float INF is not representable as an int".

  $padSeqMaxSeconds = INF;

?>
