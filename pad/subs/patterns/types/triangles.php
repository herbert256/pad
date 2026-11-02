<?php

  // triangles - a mosaic of equilateral triangles of the size, pointing up and down by turns,
  // their inks running from the first at the top left to the last at the bottom right with
  // the seed's scatter: a low-poly gradient. Write the inks from light to dark, or back.
  //
  //   {pattern:triangles size=32, seed=11/}

  return [

    'size'    => [ 40, 6, 400 ],
    'colors'  => [ '#ffffff', '#d6eef0', '#a8dadc', '#7fb3c9', '#457b9d', '#2b5a83', '#1d3557' ],
    'count'   => [ 'triangles', 50000, fn ( $p ) => (int) ceil ( $p ['h'] / ( $p ['size'] * sqrt ( 3 ) / 2 ) ) * ( (int) ceil ( 2 * $p ['w'] / $p ['size'] ) + 1 ),
                   'a larger size or a smaller width and height' ],

    'draw'    => function ( $p ) {

      $s     = $p ['size'];
      $h     = $s * sqrt ( 3 ) / 2;
      $n     = count ( $p ['ink'] );
      $paths = array_fill ( 0, $n, '' );
      $up    = 'l' . padPatternNum ( $s / 2 ) . ',-' . padPatternNum ( $h ) . 'l' . padPatternNum ( $s / 2 ) . ',' . padPatternNum ( $h ) . 'z';
      $down  = 'h' . padPatternNum ( $s ) . 'l-' . padPatternNum ( $s / 2 ) . ',' . padPatternNum ( $h ) . 'z';
      $rows  = (int) ceil ( $p ['h'] / $h );
      $cols  = (int) ceil ( 2 * $p ['w'] / $s ) - 1;

      for ( $j = 0; $j < $rows; $j++ )
        for ( $k = -1; $k <= $cols; $k++ ) {
          $x    = $k * $s / 2;
          $y    = $j * $h;
          $t    = 0.65 * ( $x + $s / 2 ) / $p ['w'] + 0.35 * ( $y + $h / 2 ) / $p ['h'] + ( padPatternRand () - 0.5 ) * 0.35;
          $pick = max ( 0, min ( $n - 1, (int) floor ( $t * $n ) ) );
          $paths [$pick] .= ( ( $k + $j ) % 2 == 0 )
            ? 'M' . padPatternNum ( $x ) . ',' . padPatternNum ( $y + $h ) . $up
            : 'M' . padPatternNum ( $x ) . ',' . padPatternNum ( $y ) . $down;
        }

      // A thin stroke of the fill itself: the edges that two triangles share are drawn over,
      // where anti-aliasing left a hairline of the background between them.

      $body = '';

      foreach ( $paths as $i => $d )
        if ( $d !== '' ) {
          $ink   = padPatternAttr ( $p ['ink'] [$i] );
          $body .= "<path d=\"$d\" fill=\"$ink\" stroke=\"$ink\" stroke-width=\"0.6\" stroke-linejoin=\"round\"/>";
        }

      return [ '', $body ];

    }

  ];

?>
