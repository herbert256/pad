<?php

  // blobs - soft organic shapes about the size across, scattered by the seed, each a closed
  // curve through seven points around its centre at radii of their own, filled with an ink.
  //
  //   {pattern 'blobs', size=90, seed=12, opacity=0.8/}
  //
  // The curve is a Catmull-Rom spline through the points, written as the cubic Béziers it
  // is: from each point to the next, the handles a sixth of the way along the line between
  // the points on either side.

  return [

    'size'    => [ 70, 10, 800 ],
    'colors'  => [ '#fff8f0', '#ffb4a2', '#e5989b', '#b5838d', '#6d6875' ],

    'draw'    => function ( $p ) {

      $count = (int) max ( 2, min ( 60, round ( $p ['w'] * $p ['h'] / ( $p ['size'] * $p ['size'] * 2.2 ) ) ) );
      $body  = '';

      for ( $b = 0; $b < $count; $b++ ) {

        $cx     = padPatternRand () * $p ['w'];
        $cy     = padPatternRand () * $p ['h'];
        $r      = $p ['size'] * ( 0.35 + 0.3 * padPatternRand () );
        $points = [];

        for ( $k = 0; $k < 7; $k++ ) {
          $angle    = 2 * M_PI * $k / 7 + ( padPatternRand () - 0.5 ) * 0.5;
          $radius   = $r * ( 0.72 + 0.56 * padPatternRand () );
          $points[] = [ $cx + $radius * padPatternCos ( $angle ), $cy + $radius * padPatternSin ( $angle ) ];
        }

        $at = fn ( $x, $y ) => padPatternNum ( $x, 1 ) . ',' . padPatternNum ( $y, 1 );
        $d  = 'M' . $at ( ...$points [0] );

        for ( $k = 0; $k < 7; $k++ ) {
          list ( $x0, $y0 ) = $points [ ( $k + 6 ) % 7 ];
          list ( $x1, $y1 ) = $points [$k];
          list ( $x2, $y2 ) = $points [ ( $k + 1 ) % 7 ];
          list ( $x3, $y3 ) = $points [ ( $k + 2 ) % 7 ];
          $d .= 'C' . $at ( $x1 + ( $x2 - $x0 ) / 6, $y1 + ( $y2 - $y0 ) / 6 ) . ' ' . $at ( $x2 - ( $x3 - $x1 ) / 6, $y2 - ( $y3 - $y1 ) / 6 ) . ' ' . $at ( $x2, $y2 );
        }

        $body .= "<path d=\"{$d}Z\" fill=\"" . padPatternAttr ( padPatternPick ( $p ['ink'] ) ) . '"/>';

      }

      return [ '', $body ];

    }

  ];

?>
