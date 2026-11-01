<?php

  // ecdf - The empirical cumulative distribution: a step line per group of label climbing from
  // 0 to 100%, at each value of value= by its share of the group - equal values one step, the
  // share at a value including it - nothing binned or smoothed; rows without a number counted
  // in the <desc>. Options: label, value; width= and height=, 600 x 300 unless given.

  return padChartEcdf ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
