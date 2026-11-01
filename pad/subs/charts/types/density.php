<?php

  // density - A smooth curve per group of label (Gaussian kernel density, bandwidth or else
  // Silverman's rule) over one value axis - a light wash, its line, the median dashed, a
  // legend for several groups. Options: label, value, bandwidth; width= and height=, 600 x 300
  // unless given.

  return padChartDensity ( 'density', padChartGather (), ...padChartSize ( 600, 300 ) );

?>
