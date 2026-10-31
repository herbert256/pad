<?php

  // chord - Flows between the members of one group: a node an arc around the circle as long as
  // all that flows out of and into it, a ribbon per flow through the middle as wide as the
  // flow, in the colour of its source; without value a row counts one. Options: source,
  // target, value; width= and height=, 600 x 400 unless given.

  return padChartChord ( padChartGather (), ...padChartSize ( 600, 400 ) );

?>
