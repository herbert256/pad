<?php

  // Values that differ only in their last bits - 0.3 beside 0.1 + 0.2 - are drawn as one
  // value: the step of the axis between them, added to the lowest tick, gave it back
  // unchanged, and the loop of the ticks never ended - 2 GB of memory, then a fatal error.

  include_once PAD . 'tags/_lib/chart.php';

  $ticks = implode ( ' ', padChartTicks ( 0.3, 0.1 + 0.2 ) );
  $big   = count ( padChartTicks ( 10000000000000000, 10000000000000002 ) );

?>
