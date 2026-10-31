<?php

  // rings - Concentric progress rings, one per row (eight at most, the first outermost): an
  // arc of value against to - a number (100) or a field of the row - clockwise from twelve
  // over a light track, full past the goal; a legend with value, goal and share. Options:
  // label, value, to; width= and height=, 480 x 280 unless given.

  return padChartRings ( padChartGather (), ...padChartSize ( 480, 280 ) );

?>
