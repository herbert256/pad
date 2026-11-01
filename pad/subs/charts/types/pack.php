<?php

  // pack - The treemap's hierarchy as circles in circles: a part a circle with the area of its
  // value, the parts of a group packed largest first inside a light circle, coloured by the
  // outermost group with a legend. Options: label, value, levels; width= and height=, 600 x
  // 400 unless given.

  return padChartPack ( padChartGather (), ...padChartSize ( 600, 400 ) );

?>
