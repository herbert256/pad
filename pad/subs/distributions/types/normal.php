<?php
  return [
    'parameters' => [ 'mean' => [ 0, -1e150, 1e150, FALSE ], 'sd' => [ 1, 1e-150, 1e150, FALSE ] ],
    'support' => 'real',
    'work' => fn ( $p ) => 2,
    'sample' => fn ( $p, $random ) => $p ['mean'] + $p ['sd'] * sqrt ( -2 * log ( 1 - $random () ) ) * cos ( 2 * M_PI * $random () )
  ];
?>
