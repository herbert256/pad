<?php

  // violin - A shape per group of label on a vertical value axis: the density mirrored around
  // its middle, cut at the lowest and highest value, all groups on one scale - the quartiles
  // as a thick bar and the median as a light dot inside. Options: label, value, bandwidth;
  // width= and height=, 600 x 300 unless given.

  return padChartDensity ( 'violin', padChartGather (), ...padChartSize ( 600, 300 ) );

?>
