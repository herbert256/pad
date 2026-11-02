<?php
  $measurements = [ 1, 2, 2, 3, 4, 4, 4, 5, 7, 8, NULL ];
  $description = padStats ( 'describe', $measurements );
  $histogram = padStats ( 'histogram', $measurements, [ 'edges' => [ 0, 2, 4, 6, 8 ] ] );
  $histogramRows = [];
  foreach ( $histogram ['bins'] as $bin ) $histogramRows [] = [ 'interval' => $bin ['lower'] . ' to ' . $bin ['upper'], 'count' => $bin ['count'] ];
  $summaryRows = [ $description ];
?>
