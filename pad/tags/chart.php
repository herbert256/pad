<?php

  // {chart 'bar', data='sales', label='month', value='amount'/} - an inline SVG chart of the kind
  // its parameter names, drawn by the chart subsystem in subs/charts/: a kind is a file of
  // subs/charts/types/, the same chart as the chart: type writes it, {chart:bar data='sales'/}.

  $padChartKind = strtolower ( trim ( (string) $padParm ) );

  return include PAD . 'subs/charts/chart.php';

?>
