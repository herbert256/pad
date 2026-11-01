<?php

  // waterfall - Each row a step up (green) or down (red) floating from where the step before
  // ended, a closing bar named by total ('Total'; total='' none), dashed lines carrying the
  // level, the change above each bar. Options: label, value, total; width= and height=, 600 x
  // 300 unless given.

  return padChartWaterfall ( padChartGather (), ...padChartSize ( 600, 300 ) );

?>
