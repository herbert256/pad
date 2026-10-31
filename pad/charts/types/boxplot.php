<?php

  // boxplot - A box per group of label - all values in one without it - from the first to the
  // third quartile, the median across, whiskers to the furthest values within 1.5 box heights,
  // the values past them as dots. Options: label, value; width= and height=, 600 x 300 unless
  // given.

  return padChartBoxplot ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
