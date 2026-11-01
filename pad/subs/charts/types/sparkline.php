<?php

  // sparkline - A word-sized line, no axes, to stand in a sentence or a table cell - the
  // {sparkline} tag draws it too. Options: label, value; width= and height=, 120 x 32 unless
  // given.

  return padChartPlain ( 'sparkline', padChartGather (), ...padChartSize ( 120, 32 ) );

?>
