<?php

  // circles - circle packing: circles of the inks laid where the seed tries them, each as large
  // as the room there allows up to the size, never touching, the large ones first.
  //
  //   {pattern 'circles', size=60, seed=2/}
  //
  // A place is tried at random, and the circle there is as large as the nearest circle leaves
  // room for - less a small gap - and at most the size, that most shrinking from the size to
  // the least as the tries go on, so the big circles are laid first and the small ones fill
  // the holes. A circle may run off the picture. The circles laid are kept in a grid of
  // squares twice the size, so a try looks at the squares around it alone.

  return [

    'size'    => [ 46, 6, 500 ],
    'colors'  => [ '#fdf8f0', '#ef476f', '#ffd166', '#06d6a0', '#118ab2', '#073b4c' ],

    'draw'    => function ( $p ) {

      $most  = $p ['size'];
      $least = max ( 1.5, $most / 14 );
      $gap   = max ( 0.8, $most / 30 );
      $tries = (int) min ( 8000, max ( 200, $p ['w'] * $p ['h'] / ( $least * $least ) * 0.5 ) );
      $cell  = 2 * $most + $gap;
      $grid  = [];
      $laid  = [];

      for ( $k = 0; $k < $tries; $k++ ) {

        $x  = padPatternRand () * $p ['w'];
        $y  = padPatternRand () * $p ['h'];
        $r  = $least + ( $most - $least ) * ( 1 - $k / $tries ) ** 2;
        $gx = (int) floor ( $x / $cell );
        $gy = (int) floor ( $y / $cell );

        for ( $b = $gy - 1; $b <= $gy + 1 and $r >= $least; $b++ )
          for ( $a = $gx - 1; $a <= $gx + 1 and $r >= $least; $a++ )
            foreach ( $grid [$b] [$a] ?? [] as $n ) {
              list ( $cx, $cy, $cr ) = $laid [$n];
              $r = min ( $r, sqrt ( ( $x - $cx ) ** 2 + ( $y - $cy ) ** 2 ) - $cr - $gap );
            }

        if ( $r < $least )
          continue;

        $grid [$gy] [$gx] [] = count ( $laid );
        $laid [] = [ $x, $y, $r ];

      }

      $groups = array_fill ( 0, count ( $p ['ink'] ), '' );

      foreach ( $laid as list ( $x, $y, $r ) )
        $groups [ padFakeNext () % count ( $p ['ink'] ) ] .= '<circle cx="' . padPatternNum ( $x, 1 ) . '" cy="' . padPatternNum ( $y, 1 ) . '" r="' . padPatternNum ( $r, 1 ) . '"/>';

      $body = '';

      foreach ( $groups as $i => $circles )
        if ( $circles !== '' )
          $body .= '<g fill="' . padPatternAttr ( $p ['ink'] [$i] ) . "\">$circles</g>";

      return [ '', $body ];

    }

  ];

?>
