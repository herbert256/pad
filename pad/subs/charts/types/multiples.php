<?php

  // multiples - Small multiples: a small line over a wash per group of by, all on one scale
  // that takes in zero and the same x positions, in a grid of columns (default: the count that
  // gives the best panel shape); the ticks left of the first column, the first and last label
  // under the bottom row. Options: by, label, value, columns; width= and height=, 600 x 400
  // unless given.

  return padChartMultiples ( padChartGather (), ...padChartSize ( 600, 400 ) );

?>
