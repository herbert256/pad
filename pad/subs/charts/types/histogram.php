<?php

  // histogram - The numbers of value counted in about bins (10) bins of one round width - a
  // whole width for whole numbers; a value on an edge in the bin above. Options: value, bins;
  // width= and height=, 600 x 300 unless given.

  return padChartHistogram ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
