<?php

  // bullet - A track per row with a thin bar of value and target (a field, or one number) as a
  // short upright line; bands - up to four, a number a percentage of the scale, a name a field
  // holding the threshold - split the track into grey steps; to ends the scale (a number or a
  // field), else round numbers past the row's highest. Options: label, value, target, bands,
  // to; width= and height=, 600 x 300 unless given.

  return padChartBullet ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
