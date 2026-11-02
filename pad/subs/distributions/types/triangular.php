<?php
  return [
    'parameters' => [ 'min' => [ 0, -1e150, 1e150, FALSE ], 'mode' => [ .5, -1e150, 1e150, FALSE ], 'max' => [ 1, -1e150, 1e150, FALSE ] ],
    'valid' => fn ( $p ) => $p ['min'] < $p ['max'] && $p ['mode'] >= $p ['min'] && $p ['mode'] <= $p ['max'],
    'support' => '[min,max]', 'work' => fn ( $p ) => 1,
    'sample' => function ( $p, $random ) {
      $u = $random (); $width = $p ['max'] - $p ['min']; $cut = ( $p ['mode'] - $p ['min'] ) / $width;
      return $u < $cut ? $p ['min'] + sqrt ( $u ) * sqrt ( $width ) * sqrt ( $p ['mode'] - $p ['min'] )
        : $p ['max'] - sqrt ( 1 - $u ) * sqrt ( $width ) * sqrt ( $p ['max'] - $p ['mode'] );
    }
  ];
?>
