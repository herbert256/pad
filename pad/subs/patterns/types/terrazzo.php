<?php

  // terrazzo - chips of stone set in a ground of the background: angular pieces of the inks
  // the seed picks, a few as large as the size and many small ones filling in between them,
  // each turned its own way, none touching another.
  //
  //   {pattern 'terrazzo', size=36, seed=5, colors='#f2eee8, #d1495b, #edae49, #00798c, #30638e'/}
  //
  // A chip is tried at a place the seed picks, as large as the room there allows - its corners
  // within a circle that keeps clear of every chip laid, less a gap of an eighth of the size -
  // and at most the size across, that most shrinking to a sixth of it as the tries go on, so
  // the large chips are laid first and the small ones fill the holes, a third of the room left
  // free so the ground shows; every square of the size is tried 32 times. A chip is four to
  // seven corners around its middle, each at an angle and a distance of its own; it may run off
  // the picture. The chips laid are kept in a grid of squares of the size and the gap, so a try
  // looks at the squares around it alone. A chip is written from its first corner, each next
  // one from the one before.

  return [

    'size'    => [ 30, 6, 400 ],
    'colors'  => [ '#f3efe8', '#e3a587', '#9fb8ad', '#d9c6a5', '#8a8f99', '#c97b63' ],

    // Each square of the size is tried 32 times: 5,000 squares are 160,000 tries, under a
    // second.

    'count'   => [ 'squares of its size', 5000, fn ( $p ) => (int) ceil ( $p ['w'] / $p ['size'] ) * (int) ceil ( $p ['h'] / $p ['size'] ),
                   'a larger size or a smaller width and height' ],

    'draw'    => function ( $p ) {

      $most  = $p ['size'] / 2;
      $least = max ( 1, $most / 6 );
      $gap   = max ( 1, $most / 4 );
      $tries = (int) max ( 120, 8 * $p ['w'] * $p ['h'] / ( $most * $most ) );
      $cell  = 2 * $most + $gap;
      $grid  = [];
      $laid  = [];

      for ( $k = 0; $k < $tries; $k++ ) {

        $x  = padPatternRand () * $p ['w'];
        $y  = padPatternRand () * $p ['h'];
        $r  = $least + ( $most - $least ) * ( 1 - $k / $tries ) ** 3;
        $gx = (int) floor ( $x / $cell );
        $gy = (int) floor ( $y / $cell );

        for ( $b = $gy - 1; $b <= $gy + 1 and $r >= $least; $b++ )
          for ( $a = $gx - 1; $a <= $gx + 1 and $r >= $least; $a++ )
            foreach ( $grid [$b] [$a] ?? [] as $n ) {
              list ( $cx, $cy, $cr ) = $laid [$n];
              $r = min ( $r, ( sqrt ( ( $x - $cx ) ** 2 + ( $y - $cy ) ** 2 ) - $cr - $gap ) * 0.67 );
            }

        if ( $r < $least )
          continue;

        $grid [$gy] [$gx] [] = count ( $laid );
        $laid [] = [ $x, $y, $r ];

      }

      // The chips, in the order laid: a number of corners, a turn, then each corner's angle
      // and distance - the inks picked last.

      $paths = array_fill ( 0, count ( $p ['ink'] ), '' );

      foreach ( $laid as list ( $x, $y, $r ) ) {
        $corners = 4 + padFakeNext () % 4;
        $turn    = 2 * M_PI * padPatternRand ();
        $d       = '';
        $at      = NULL;
        for ( $c = 0; $c < $corners; $c++ ) {
          $angle  = $turn + 2 * M_PI * ( $c + 0.7 * ( padPatternRand () - 0.5 ) ) / $corners;
          $reach  = $r * ( 0.55 + 0.45 * padPatternRand () );
          $to     = [ round ( $x + $reach * padPatternCos ( $angle ), 1 ), round ( $y + $reach * padPatternSin ( $angle ), 1 ) ];
          $d     .= ( $at === NULL ) ? 'M' . padPatternNum ( $to [0], 1 ) . ',' . padPatternNum ( $to [1], 1 ) . 'l'
                                     : padPatternNum ( $to [0] - $at [0], 1 ) . ',' . padPatternNum ( $to [1] - $at [1], 1 ) . ' ';
          $at     = $to;
        }
        $paths [ padFakeNext () % count ( $p ['ink'] ) ] .= rtrim ( $d ) . 'z';
      }

      $body = '';

      foreach ( $paths as $i => $d )
        if ( $d !== '' )
          $body .= "<path d=\"$d\" fill=\"" . padPatternAttr ( $p ['ink'] [$i] ) . '"/>';

      return [ '', $body ];

    }

  ];

?>
