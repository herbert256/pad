<?php

  // dumbbell - Two values per row joined by a line: a row per category (label), a dot at from
  // in the first colour and one at to in the second, a legend naming the two ends (names=,
  // else the fields), all rows on one horizontal scale; ranked orders them by their change,
  // a row with one end draws that dot alone. Options: label, from, to, names, ranked; width=
  // and height=, 600 x 300 unless given.

  return padChartDumbbell ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
