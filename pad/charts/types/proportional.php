<?php

  // proportional - A circle per row - a square with square - whose area is value, side by side
  // on a common baseline, wrapping into lines, the value and label under each. Options: label,
  // value, square; width= and height=, 600 x 300 unless given.

  return padChartProportional ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
