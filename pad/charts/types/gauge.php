<?php

  // gauge - One number on a half ring from from (0) to to (100); bands - one or two thresholds
  // - colour the track green, amber, red; target marks a value; unit follows the number. value
  // is the number itself, or the field of the first row - so a gauge needs no data. Options:
  // value, from, to, bands, target, label, unit; width= and height=, 320 x 200 unless given.
  //
  // A gauge of one number needs no rows: given value= and no data, sequence or content it draws
  // that value - padChartGather ( TRUE ).

  return padChartGauge ( padChartGather ( TRUE ), ...padChartSize ( 320, 200 ) );

?>
