<?php

  // Type handler for a chart kind: {chart:bar data='sales'/}, or a bare tag naming a file of
  // charts/types/ when nothing else has the name, {gauge value=72/}. The kind is the tag's
  // name, and the chart is drawn as {chart 'bar'} draws it, by charts/chart.php - run as a
  // built-in tag's PHP half is run, types/_go/tag.php.

  $padChartKind = $padTag [$pad];

  $padTagGo = PAD . 'charts/chart';

  return include PAD . 'types/_go/tag.php';

?>
