<?php

  // hexbin - Many points on two numeric axes, counted in a grid of hexagons: bins= across the
  // plot (20), each hexagon coloured by the points nearest its middle in six steps, the scale
  // above; the counts add up to the points drawn, a row without two numbers is counted apart.
  // Options: x, y, bins; width= and height=, 600 x 400 unless given.

  return padChartHexbin ( padChartGather (), ...padChartSize ( 600, 400 ) );

?>
