<?php

  // Type handler for a chart kind: {chart:bar data='sales'/}, or a bare tag naming a file of
  // subs/charts/types/ when nothing else has the name, {gauge value=72/}. The kind is the tag's
  // name, and the chart is drawn as {chart 'bar'} draws it, by subs/charts/chart.php - run as a
  // built-in tag's PHP half is run, types/_go/tag.php.

  $padChartKind = $padTag [$pad];

  $padTagGo = PAD . 'subs/charts/chart';

  return include PAD . 'types/_go/tag.php';

?>
