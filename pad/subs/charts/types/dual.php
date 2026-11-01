<?php

  // dual - Two measures on two axes: value as bars on the left axis, line as a line with
  // ringed points on the right one, each axis's ticks in its series' colour and the right
  // ticks on the left's grid lines when their zeros can line up; a legend names both. Options:
  // label, value, line; width= and height=, 600 x 300 unless given.

  return padChartDual ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
