<?php

  // marimekko - A column per row as wide as its total of the value fields (two or more - else
  // every numeric field), each field stacked as its share of the column to 100%, the
  // percentage in each part with room; a 0-100% axis on the left, names and totals below, a
  // legend. Options: label, value; width= and height=, 600 x 300 unless given.

  return padChartMarimekko ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
