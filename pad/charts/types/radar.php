<?php

  // radar - A spoke per measure of value (three or more), a polygon per row named by label
  // (eight at most), rings from 0 to to - else round numbers past the highest value. Options:
  // label, value, to; width= and height=, 600 x 400 unless given.

  return padChartRadar ( padChartGather (), ...padChartSize ( 600, 400 ) );

?>
