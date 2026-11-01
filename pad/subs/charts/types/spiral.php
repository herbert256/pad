<?php

  // spiral - A long series wound clockwise from twelve o'clock along an Archimedean spiral,
  // period rows (12) to a turn; each row a segment of the band in seven steps of one hue, the
  // scale below; the labels of the first turn around the outside, turn names each turn where
  // it begins. Options: label, value, period, turn; width= and height=, 480 x 420 unless
  // given.

  return padChartSpiral ( padChartGather (), ...padChartSize ( 480, 420 ) );

?>
