<?php

  // scatter - A dot per row on two numeric axes; color colours by a field (eight groups, the
  // rest grey), trend adds the least-squares line. Options: x, y, label, color, trend; width=
  // and height=, 600 x 300 unless given.

  return padChartScatter ( 'scatter', padChartGather (), padChartSeqName (), ...padChartSize ( 600, 300 ) );

?>
