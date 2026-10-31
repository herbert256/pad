<?php

  // arc - Nodes on a line in the order they first appear, a dot as large as its number of
  // links, its name under it (turned when names would touch); a link a half circle above the
  // line, value for thicker links. Options: source, target, value; width= and height=, 600 x
  // 300 unless given.

  return padChartArcDiagram ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
