<?php

  // rose - Nightingale's polar area chart: a wedge per row of one angle, the first centred on
  // twelve o'clock, its area its value (radius by the square root); several value fields lie
  // outward as rings, a colour each with a legend; names around the circle, rings of round
  // numbers. Options: label, value; width= and height=, 480 x 280 unless given.

  return padChartRadial ( 'rose', padChartGather (), ...padChartSize ( 480, 280 ) );

?>
