<?php

  // hexagons - a honeycomb of flat-topped hexagons, the size their radius, each filled with an
  // ink the seed picks and set a little apart, so the background shows as the mortar between
  // them.
  //
  //   {pattern 'hexagons', size=14, seed=3/}

  return [

    'size'    => [ 18, 4, 300 ],
    'colors'  => [ '#f8f6f1', '#264653', '#2a9d8f', '#e9c46a', '#f4a261', '#e76f51' ],

    'draw'    => function ( $p ) {

      $r     = $p ['size'];
      $h     = sqrt ( 3 ) * $r;
      $rr    = $r * 0.92;
      $hh    = sqrt ( 3 ) * $rr;
      $cols  = (int) ceil ( $p ['w'] / ( 1.5 * $r ) ) + 1;
      $rows  = (int) ceil ( $p ['h'] / $h ) + 1;
      $paths = array_fill ( 0, count ( $p ['ink'] ), '' );
      $step  = 'l' . padPatternNum ( $rr / 2 ) . ',-' . padPatternNum ( $hh / 2 ) . 'h' . padPatternNum ( $rr )
             . 'l' . padPatternNum ( $rr / 2 ) . ',' . padPatternNum ( $hh / 2 ) . 'l-' . padPatternNum ( $rr / 2 ) . ',' . padPatternNum ( $hh / 2 )
             . 'h-' . padPatternNum ( $rr ) . 'z';

      // Every hexagon that reaches into the picture, column by column.

      for ( $c = 0; $c <= $cols; $c++ )
        for ( $j = -1; $j <= $rows; $j++ ) {
          $cx = $c * 1.5 * $r;
          $cy = $j * $h + ( $c % 2 ? $h / 2 : 0 );
          if ( $cx - $rr >= $p ['w'] or $cy - $hh / 2 >= $p ['h'] or $cy + $hh / 2 <= 0 )
            continue;
          $paths [ padFakeNext () % count ( $p ['ink'] ) ] .= 'M' . padPatternNum ( $cx - $rr ) . ',' . padPatternNum ( $cy ) . $step;
        }

      $body = '';

      foreach ( $paths as $i => $d )
        if ( $d !== '' )
          $body .= "<path d=\"$d\" fill=\"" . padPatternAttr ( $p ['ink'] [$i] ) . '"/>';

      return [ '', $body ];

    }

  ];

?>
