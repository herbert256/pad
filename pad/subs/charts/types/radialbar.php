<?php

  // radialbar - A ring per row, the first outside, an arc from twelve o'clock over three
  // quarters of the circle as far as its value against to - else round numbers past the
  // highest value - on a light track of its colour; the name at the start of the track, the
  // value at the end of the arc. Options: label, value, to; width= and height=, 480 x 280
  // unless given.

  return padChartRadial ( 'radialbar', padChartGather (), ...padChartSize ( 480, 280 ) );

?>
