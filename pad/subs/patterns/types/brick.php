<?php

  // brick - a wall of bricks laid in courses: the size a brick's height and ratio= times that
  // its length (3), each course shifted along the one below by offset= of a brick's length
  // (0.5, the running bond; 0 stacks them, a third or a quarter rakes them), the background
  // the mortar of the joints, joint= pixels wide (2). One ink lays every brick in it; with more
  // the seed picks each brick's ink, so the wall is mottled. A tile repeated by a <pattern>.
  //
  //   {pattern:brick size=14, ratio=2, offset=0.25, colors='#d9d4cc, #a4472f, #b5563b, #8f3d2a'/}
  //
  // The offset is taken in twelfths of a brick - a half, thirds, quarters, sixths - so the
  // courses come back to the first after twelve at most: the tile is as many courses as that
  // takes - at least ten, and eleven bricks wide, when the seed picks the inks - each brick
  // that leaves it on one side the same brick coming in on the other. The joint is at most
  // nearly half a brick's height; a brick is set in by half of it on every side, so two meet
  // the joint apart.

  return [

    'size'    => [ 16, 3, 300 ],
    'colors'  => [ '#f1ece4', '#e3d6c4', '#dccbb6', '#e8ddce' ],
    'options' => [ 'ratio'  => [ 3, 1, 8 ],
                   'offset' => [ 0.5, 0, 1 ],
                   'joint'  => [ 2, 0, 40 ] ],

    'draw'    => function ( $p ) {

      $high  = $p ['size'];
      $long  = $high * $p ['ratio'];
      $joint = min ( $p ['joint'], 0.45 * $high );
      $shift = (int) round ( $p ['offset'] * 12 ) % 12;
      $back  = 1;

      while ( ( $back * $shift ) % 12 )
        $back++;

      $n     = count ( $p ['ink'] );
      $rows  = ( $n > 1 ) ? $back * (int) ceil ( 10 / $back ) : $back;
      $cols  = ( $n > 1 ) ? 11 : 1;
      $paths = array_fill ( 0, $n, '' );
      $pick  = [];

      // The inks of the tile's bricks, course by course - picked in this order, the same
      // every time.

      for ( $j = 0; $j < $rows; $j++ )
        for ( $i = 0; $i < $cols; $i++ )
          $pick [$j] [$i] = ( $n > 1 ) ? padFakeNext () % $n : 0;

      $w = padPatternNum ( $long - $joint );
      $h = padPatternNum ( $high - $joint );

      // The bricks of a course: from the one cut by the left edge to the one cut by the right,
      // which is the same brick - the course's offset from the one below, counted from the
      // bottom of the tile.

      for ( $j = 0; $j < $rows; $j++ ) {
        $from = ( ( ( $rows - 1 - $j ) * $shift ) % 12 ) / 12 * $long;
        for ( $i = -1; $i < $cols; $i++ ) {
          $x = $from + $i * $long;
          if ( $x + $long <= 0 )
            continue;
          $paths [ $pick [$j] [ ( $i + $cols ) % $cols ] ] .= 'M' . padPatternNum ( $x + $joint / 2 ) . ',' . padPatternNum ( $j * $high + $joint / 2 ) . "h{$w}v{$h}h-{$w}z";
        }
      }

      $body = '';

      foreach ( $paths as $i => $d )
        if ( $d !== '' )
          $body .= "<path d=\"$d\" fill=\"" . padPatternAttr ( $p ['ink'] [$i] ) . '"/>';

      return [ "<pattern id=\"{$p ['id']}\" width=\"" . padPatternNum ( $cols * $long ) . '" height="' . padPatternNum ( $rows * $high ) . "\" patternUnits=\"userSpaceOnUse\">$body</pattern>",
               padPatternFill ( $p, $p ['id'] ) ];

    }

  ];

?>
