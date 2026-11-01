<?php

  // network - Nodes and the links between them; layout='force' (default, the same picture for
  // the same data; past 300 nodes they are set on a circle, which the description says) or
  // 'radial', arrows for the direction, value for thicker links. Options: source, target,
  // value, layout, arrows; width= and height=, 600 x 400 unless given.

  return padChartNetwork ( padChartGather (), ...padChartSize ( 600, 400 ) );

?>
