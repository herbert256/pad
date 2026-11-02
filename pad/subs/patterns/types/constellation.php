<?php

  // constellation - stars joined to their neighbours by lines: the seed scatters them about
  // the size apart and a line joins two that are near, the fainter the farther they are - the
  // lines in the first ink, the stars in the second.
  //
  //   {pattern:constellation size=60, seed=12, colors='#0b1021, #5c6f9e, #e8ecf8'/}
  //
  // A star lies in every square of the size, at a place in it the seed picks - not too near its
  // edges - a few of them larger. Two stars closer than one and a half sizes are joined; the
  // lines are three strengths, the nearest drawn strongest. The stars at the picture's edge
  // look past it: a star lies in the squares around the picture too, so the lines go on out
  // of it - a line wholly beside the picture is left out, and a star outside it.

  return [

    'size'    => [ 56, 12, 600 ],
    'colors'  => [ '#f6f7fb', '#a9b4c8', '#5d6b85' ],

    // A star is some three lines and a circle of SVG: 10,000 stars are about a megabyte.

    'count'   => [ 'stars', 10000, fn ( $p ) => ( (int) ceil ( $p ['w'] / $p ['size'] ) + 2 ) * ( (int) ceil ( $p ['h'] / $p ['size'] ) + 2 ),
                   'a larger size or a smaller width and height' ],

    'draw'    => function ( $p ) {

      $s     = $p ['size'];
      $cols  = (int) ceil ( $p ['w'] / $s );
      $rows  = (int) ceil ( $p ['h'] / $s );
      $stars = [];

      for ( $j = -1; $j <= $rows; $j++ )
        for ( $i = -1; $i <= $cols; $i++ )
          $stars [$j] [$i] = [ ( $i + 0.15 + 0.7 * padPatternRand () ) * $s, ( $j + 0.15 + 0.7 * padPatternRand () ) * $s,
                               padPatternRand () < 0.12 ? 2.2 : 1 ];

      // Each pair once: a star and the stars after it in the squares around it.

      $lines = [ '', '', '' ];
      $reach = 1.5 * $s;
      $at    = fn ( $x, $y ) => padPatternNum ( $x, 1 ) . ',' . padPatternNum ( $y, 1 );

      for ( $j = -1; $j <= $rows; $j++ )
        for ( $i = -1; $i <= $cols; $i++ ) {
          list ( $x, $y ) = $stars [$j] [$i];
          foreach ( [ [ 1, 0 ], [ -1, 1 ], [ 0, 1 ], [ 1, 1 ], [ 2, 0 ], [ 0, 2 ], [ 2, 1 ], [ 1, 2 ], [ -1, 2 ], [ -2, 1 ] ] as list ( $di, $dj ) ) {
            if ( ! isset ( $stars [ $j + $dj ] [ $i + $di ] ) )
              continue;
            list ( $u, $v ) = $stars [ $j + $dj ] [ $i + $di ];
            if ( max ( $x, $u ) < 0 or max ( $y, $v ) < 0 or min ( $x, $u ) > $p ['w'] or min ( $y, $v ) > $p ['h'] )
              continue;
            $far = sqrt ( ( $u - $x ) ** 2 + ( $v - $y ) ** 2 );
            if ( $far < $reach )
              $lines [ min ( 2, (int) floor ( 3 * $far / $reach ) ) ] .= 'M' . $at ( $x, $y ) . 'L' . $at ( $u, $v );
          }
        }

      $ink   = padPatternAttr ( $p ['ink'] [0] );
      $width = padPatternNum ( max ( 0.6, $s / 45 ) );
      $body  = "<g stroke=\"$ink\" stroke-width=\"$width\" stroke-linecap=\"round\">";

      foreach ( $lines as $k => $d )
        if ( $d !== '' )
          $body .= "<path d=\"$d\" stroke-opacity=\"" . [ '0.9', '0.55', '0.25' ] [$k] . '"/>';

      // The stars over the lines, a circle each - those in the picture.

      $dot   = max ( 1, $s / 28 );
      $body .= '</g><g fill="' . padPatternAttr ( $p ['ink'] [1] ?? $p ['ink'] [0] ) . '">';

      foreach ( $stars as $row )
        foreach ( $row as list ( $x, $y, $big ) )
          if ( $x > - $dot * $big and $y > - $dot * $big and $x < $p ['w'] + $dot * $big and $y < $p ['h'] + $dot * $big )
            $body .= '<circle cx="' . padPatternNum ( $x, 1 ) . '" cy="' . padPatternNum ( $y, 1 ) . '" r="' . padPatternNum ( $dot * $big, 1 ) . '"/>';

      return [ '', "$body</g>" ];

    }

  ];

?>
