<?php

  // line - A line over a light wash, the last point marked; several value fields a line each,
  // named at the end. Options: label, value; width= and height=, 600 x 300 unless given.

  return padChartPlain ( 'line', padChartGather (), ...padChartSize ( 600, 300 ) );

?>
