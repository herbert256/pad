<?php

  // errorbar - An estimate per row with the interval around it, already computed: a point at
  // value and an upright whisker with caps from low to high, the categories (label) along the
  // bottom, one scale taking in every bound; interval names what the whisker is in the legend,
  // never made up. A row without both bounds draws its point alone, a low above its high is an
  // error naming the row. Options: label, value, low, high, interval; width= and height=,
  // 600 x 300 unless given.

  return padChartErrorbar ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
