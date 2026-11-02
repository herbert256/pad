<?php
  return [
    'parameters' => [ 'min' => [ 0, -1e150, 1e150, FALSE ], 'max' => [ 1, -1e150, 1e150, FALSE ] ],
    'support' => '[min,max)',
    'valid' => fn ( $p ) => $p ['min'] < $p ['max'],
    'work' => fn ( $p ) => 1,
    'sample' => fn ( $p, $random ) => $p ['min'] + ( $p ['max'] - $p ['min'] ) * $random ()
  ];
?>
