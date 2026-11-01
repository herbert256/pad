<?php

  // flowfield - streamlines of a current the seed makes up: lines that follow the flow, the
  // size apart and never closer than half of it, so the picture is evenly full of them, its
  // swirls some twenty sizes across. Each line is an ink the seed picks.
  //
  //   {pattern 'flowfield', size=12, seed=8, colors='#0f1b2d, #7dd3c0, #4aa3c7, #f4d06f'/}
  //
  // The flow's direction at a place is a sum of three sine waves, each running a direction, at
  // a length and a phase of the seed's, taken as an angle around one main direction. The lines
  // are laid as Jobard and Lefer lay evenly spaced streamlines: a line is traced both ways
  // from its start, a third of the size a step (the midpoint method), until it leaves the
  // picture, comes within half the size of another line - or of itself further back - or has
  // gone two hundred steps; the next starts are tried the size away on either side of every
  // second point of the lines laid, and when they run out at a square of the size not yet
  // reached, in an order the seed shuffles - a start taken only where no line passes within
  // the size. A line of fewer than six points is dropped. The points laid are kept in a grid
  // of squares half the size, so a test looks at the squares around it alone; a line is
  // written by every second point, each from the one before.

  return [

    'size'    => [ 14, 4, 200 ],
    'colors'  => [ '#f4f6f8', '#9fb7c9', '#6f93ad', '#c9b48f' ],

    // Every square of the size is tried as a start: 20,000 squares are some 60,000 points
    // traced, under a second.

    'count'   => [ 'squares of its size', 20000, fn ( $p ) => (int) ceil ( $p ['w'] / $p ['size'] ) * (int) ceil ( $p ['h'] / $p ['size'] ),
                   'a larger size or a smaller width and height' ],

    'draw'    => function ( $p ) {

      $w    = $p ['w'];
      $h    = $p ['h'];
      $sep  = $p ['size'];
      $near = $sep / 2;
      $step = $sep / 3;
      $cell = $sep / 2;
      $span = 20 * $sep;
      $main = 2 * M_PI * padPatternRand ();

      // The three waves of the flow: a direction, a length about the swirls' span, a phase
      // and a weight each.

      $waves = [];

      for ( $k = 0; $k < 3; $k++ ) {
        $turn     = 2 * M_PI * padPatternRand ();
        $length   = $span * ( 0.6 + 0.8 * padPatternRand () );
        $waves [] = [ padPatternCos ( $turn ) * 2 * M_PI / $length, padPatternSin ( $turn ) * 2 * M_PI / $length,
                      2 * M_PI * padPatternRand (), 0.5 + padPatternRand () ];
      }

      $total = array_sum ( array_column ( $waves, 3 ) );

      $angle = function ( $x, $y ) use ( $waves, $total, $main ) {
        $sum = 0;
        foreach ( $waves as list ( $fx, $fy, $phase, $weight ) )
          $sum += $weight * padPatternSin ( $fx * $x + $fy * $y + $phase );
        return $main + 1.2 * M_PI * $sum / $total;
      };

      $grid  = [];
      $lines = [];

      // Whether a point lies within $room of a point laid - of another line, or of this line
      // more than a few steps back.

      $taken = function ( $x, $y, $room, $line, $index ) use ( &$grid, $cell, $step ) {
        $gx    = (int) floor ( $x / $cell );
        $gy    = (int) floor ( $y / $cell );
        $reach = (int) ceil ( $room / $cell );
        for ( $b = $gy - $reach; $b <= $gy + $reach; $b++ )
          for ( $a = $gx - $reach; $a <= $gx + $reach; $a++ )
            foreach ( $grid [$b] [$a] ?? [] as list ( $px, $py, $pl, $pi ) )
              if ( ( $pl !== $line or abs ( $pi - $index ) > 3 * $room / $step ) and ( $px - $x ) ** 2 + ( $py - $y ) ** 2 < $room * $room )
                return TRUE;
        return FALSE;
      };

      // A point laid goes into the grid at once, so a line keeps away from itself too; a line
      // dropped takes its points out again, the last ones of their squares.

      $lay = function ( $x, $y, $line, $index ) use ( &$grid, $cell ) {
        $gy = (int) floor ( $y / $cell );
        $gx = (int) floor ( $x / $cell );
        $grid [$gy] [$gx] [] = [ $x, $y, $line, $index ];
        return [ $gy, $gx ];
      };

      $outside = fn ( $x, $y ) => $x < - $sep or $y < - $sep or $x > $w + $sep or $y > $h + $sep;

      // A line from a start, or NULL: traced forward and backward, the backward half reversed
      // in front of the start.

      $trace = function ( $sx, $sy ) use ( &$grid, &$lines, $taken, $lay, $angle, $outside, $sep, $near, $step ) {

        $line = count ( $lines );

        if ( $outside ( $sx, $sy ) or $taken ( $sx, $sy, $sep * 0.99, -1, 0 ) )
          return NULL;

        $laid   = [ $lay ( $sx, $sy, $line, 0 ) ];
        $halves = [];

        foreach ( [ 1, -1 ] as $way ) {
          $x     = $sx;
          $y     = $sy;
          $trace = [];
          for ( $n = 1; $n <= 200; $n++ ) {
            $a  = $angle ( $x, $y );
            $mx = $x + $way * $step / 2 * padPatternCos ( $a );
            $my = $y + $way * $step / 2 * padPatternSin ( $a );
            $a  = $angle ( $mx, $my );
            $x += $way * $step * padPatternCos ( $a );
            $y += $way * $step * padPatternSin ( $a );
            if ( $outside ( $x, $y ) or $taken ( $x, $y, $near, $line, $way * $n ) )
              break;
            $laid  [] = $lay ( $x, $y, $line, $way * $n );
            $trace [] = [ $x, $y ];
          }
          $halves [$way] = $trace;
        }

        $points = array_merge ( array_reverse ( $halves [-1] ), [ [ $sx, $sy ] ], $halves [1] );

        if ( count ( $points ) >= 6 )
          return $lines [] = $points;

        foreach ( array_reverse ( $laid ) as list ( $gy, $gx ) )
          array_pop ( $grid [$gy] [$gx] );

        return NULL;

      };

      // The starts of the squares, shuffled.

      $starts = [];

      for ( $j = 0; $j * $sep < $h; $j++ )
        for ( $i = 0; $i * $sep < $w; $i++ )
          $starts [] = [ ( $i + padPatternRand () ) * $sep, ( $j + padPatternRand () ) * $sep ];

      for ( $k = count ( $starts ) - 1; $k > 0; $k-- ) {
        $other = padFakeNext () % ( $k + 1 );
        list ( $starts [$k], $starts [$other] ) = [ $starts [$other], $starts [$k] ];
      }

      // Every line laid offers starts the size away on either side of it; a square's start
      // when none of them is left.

      $next = 0;

      foreach ( $starts as list ( $sx, $sy ) ) {

        if ( ! $trace ( $sx, $sy ) )
          continue;

        for ( ; $next < count ( $lines ); $next++ ) {
          $points = $lines [$next];
          for ( $k = 1; $k < count ( $points ); $k += 2 ) {
            list ( $ax, $ay ) = $points [$k - 1];
            list ( $bx, $by ) = $points [$k];
            $long = sqrt ( ( $bx - $ax ) ** 2 + ( $by - $ay ) ** 2 ) ?: 1;
            foreach ( [ 1, -1 ] as $side )
              $trace ( $bx - $side * $sep * ( $by - $ay ) / $long, $by + $side * $sep * ( $bx - $ax ) / $long );
          }
        }

      }

      $paths = array_fill ( 0, count ( $p ['ink'] ), '' );

      foreach ( $lines as $points ) {
        $last = array_key_last ( $points );
        $at   = [ round ( $points [0] [0], 1 ), round ( $points [0] [1], 1 ) ];
        $d    = 'M' . padPatternNum ( $at [0], 1 ) . ',' . padPatternNum ( $at [1], 1 ) . 'l';
        foreach ( $points as $n => list ( $x, $y ) )
          if ( $n > 0 and ( $n % 2 == 0 or $n == $last ) ) {
            $to   = [ round ( $x, 1 ), round ( $y, 1 ) ];
            $d   .= padPatternNum ( $to [0] - $at [0], 1 ) . ',' . padPatternNum ( $to [1] - $at [1], 1 ) . ' ';
            $at   = $to;
          }
        $paths [ padFakeNext () % count ( $p ['ink'] ) ] .= rtrim ( $d );
      }

      $body = '';

      foreach ( $paths as $i => $d )
        if ( $d !== '' )
          $body .= "<path d=\"$d\" stroke=\"" . padPatternAttr ( $p ['ink'] [$i] ) . '"/>';

      return [ '', '<g fill="none" stroke-width="' . padPatternNum ( max ( 0.5, $sep * 0.18 ) ) . "\" stroke-linecap=\"round\" stroke-linejoin=\"round\">$body</g>" ];

    }

  ];

?>
