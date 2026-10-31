<?php

  // {chart 'bar', data='sales', label='month', value='amount'/} - an inline SVG chart of the kind
  // its parameter names, drawn by the chart subsystem in charts/: a kind is a file of
  // charts/types/, the same chart as the chart: type writes it, {chart:bar data='sales'/}.

  $padChartKind = strtolower ( trim ( (string) $padParm ) );

  return include PAD . 'charts/chart.php';

?>
