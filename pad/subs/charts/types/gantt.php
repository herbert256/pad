<?php

  // gantt - A row per task (label), a bar from from to to - dates, or plain numbers when all
  // are; color colours by a field with a legend, progress (0-100) draws the part done full and
  // the rest light, mark a dashed line at a date. Options: label, from, to, color, progress,
  // mark; width= and height=, 600 x 300 unless given.

  return padChartGantt ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
