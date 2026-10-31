<?php

  // funnel - The rows top to bottom as centred bars as wide as their value against the
  // largest, joined by light bands; the value in the bar, the share of the first step on the
  // right, the share of the step before in the band. Options: label, value; width= and
  // height=, 600 x 300 unless given.

  return padChartFunnel ( 'funnel', padChartGather (), ...padChartSize ( 600, 300 ) );

?>
