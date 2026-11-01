<?php

  // tartan - a plaid whose sett the seed makes up: three to six stripes of the background and
  // the inks, each so many threads wide, mirrored about the first and the last as most tartans
  // are, woven the same way across and down - so where two stripes cross their colours mix, and
  // a fine twill runs over the cloth at 45 degrees. The size is the sett, the repeat, in pixels.
  // A tile repeated by a <pattern>, the seed choosing the sett.
  //
  //   {pattern 'tartan', size=120, seed=3, colors='#1f3a5f, #2e6b4f, #c0392b, #f1c40f'/}
  //
  // The sett: the number of stripes, then for each a colour - never the one of the stripe
  // before it - and a count of threads from 2 to 24, the background's twice as wide; the
  // threads of the mirrored sett are spread over the size. The stripes across are drawn whole,
  // those down over them at half opacity; the twill is a hairline in the background every two
  // threads - or every 3 pixels when the threads are finer - a whole number of times in the
  // size, so the lines meet over the edges of the tile.

  return [

    'size'    => [ 96, 16, 1000 ],
    'colors'  => [ '#f4efe4', '#b9c7cf', '#c97b63', '#5b7083' ],

    'draw'    => function ( $p ) {

      $size    = $p ['size'];
      $palette = array_merge ( [ $p ['bg'] ], $p ['ink'] );
      $count   = 3 + padFakeNext () % 4;
      $stripes = [];
      $last    = -1;

      for ( $k = 0; $k < $count; $k++ ) {
        $color = padFakeNext () % count ( $palette );
        if ( $color == $last and count ( $palette ) > 1 )
          $color = ( $color + 1 + padFakeNext () % ( count ( $palette ) - 1 ) ) % count ( $palette );
        $threads    = padPatternPick ( [ 2, 4, 4, 6, 8, 8, 12, 16, 24 ] ) * ( $color == 0 ? 2 : 1 );
        $stripes [] = [ $color, $threads ];
        $last       = $color;
      }

      // The sett mirrored: the first and the last stripe the pivots, once each.

      $sett  = array_merge ( $stripes, array_reverse ( array_slice ( $stripes, 1, -1 ) ) );
      $total = array_sum ( array_column ( $sett, 1 ) );
      $one   = $size / $total;
      $s     = padPatternNum ( $size );
      $cross = [];
      $down  = [];
      $at    = 0;

      foreach ( $sett as list ( $color, $threads ) ) {
        $from  = padPatternNum ( $at * $one );
        $wide  = padPatternNum ( $threads * $one );
        $fill  = padPatternAttr ( $palette [$color] );
        $cross [] = "<rect y=\"$from\" width=\"$s\" height=\"$wide\" fill=\"$fill\"/>";
        $down  [] = "<rect x=\"$from\" width=\"$wide\" height=\"$s\" fill=\"$fill\"/>";
        $at   += $threads;
      }

      // The twill: a line from every second thread on the top edge down to the right, its
      // part past the right edge coming back in on the left.

      $twill = '';
      $lines = max ( 1, min ( intdiv ( $total, 2 ), (int) floor ( $size / 3 ) ) );
      $step  = $size / $lines;

      for ( $k = - $lines; $k < $lines; $k++ )
        $twill .= 'M' . padPatternNum ( $k * $step ) . ",0l$s,$s";

      return [ "<pattern id=\"{$p ['id']}\" width=\"$s\" height=\"$s\" patternUnits=\"userSpaceOnUse\">"
               . implode ( '', $cross ) . '<g opacity="0.5">' . implode ( '', $down ) . '</g>'
               . "<path d=\"$twill\" stroke=\"" . padPatternAttr ( $p ['bg'] ) . '" stroke-width="' . padPatternNum ( max ( 0.4, $one / 4 ) ) . '" stroke-opacity="0.2"/>'
               . '</pattern>',
               padPatternFill ( $p, $p ['id'] ) ];

    }

  ];

?>
