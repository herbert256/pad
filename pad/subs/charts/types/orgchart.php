<?php

  // orgchart - A tree top down: a box per row named by label (a second line from sub), under
  // the row whose id its parent holds; rows without a known parent are roots side by side,
  // each branch under the top its own colour; an id twice or a circle is an error. Options:
  // id, parent, label, sub; width= and height=, 600 x 400 unless given.

  return padChartOrg ( padChartGather (), ...padChartSize ( 600, 400 ) );

?>
