<?php

  // bump - ranks across several periods, with one line per item and missing periods left as gaps.
  // Each period ranks larger values first; ties share a competition rank, such as 1, 1, 3.
  //

return padChartBump(padChartGather(),...padChartSize(600,300));
