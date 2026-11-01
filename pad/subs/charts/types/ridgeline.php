<?php

  // ridgeline - A density curve per group of label, each on a baseline of its own, stacked from
  // the top down over one value axis - one bandwidth for every ridge (bandwidth=, else
  // Silverman's rule over all values), every ridge a density on one scale, never a count;
  // overlap= the bands the tallest rises (1.5), the median a light line. Options: label,
  // value, bandwidth, overlap; width= and height=, 600 x 400 unless given.

  return padChartRidgeline ( padChartGather (), ...padChartSize ( 600, 400 ) );

?>
