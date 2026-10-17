<?php

  // The right axis of a dual chart reaches the line's highest value however small: its
  // step was compared to the need against a fixed 1e-12, values below that got a step too
  // small and the line was drawn above the plot; a denormal value ended the request.

  include_once PAD . 'tags/_lib/chart.php';

  $tiny     = padChartDualTicks ( [ 0, 25, 50, 75, 100 ], 1e-300, 9e-300, 5 );
  $tiny     = end ( $tiny ) >= 9e-300 ? 'covered' : 'cut at ' . end ( $tiny );
  $denormal = count ( padChartDualTicks ( [ 0, 25, 50, 75, 100 ], 5e-324, 1e-323, 5 ) ) ? 'drawn' : 'none';

?>
