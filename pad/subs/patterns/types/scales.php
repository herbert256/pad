<?php

  // scales - overlapping scales, row over row: the size their radius, each row laid half a
  // scale along and half a radius lower over the one above, so only the top of every scale
  // shows; a scale is a disc of an ink with rings= circles in it (3; 1 is a plain fish scale,
  // more the Japanese seigaiha of waves), its edge and rings drawn in the background. The inks
  // take turns row by row. A tile repeated by a <pattern>: the seed changes nothing.
  //
  //   {pattern 'scales', size=24, rings=4, colors='#f8f4ea, #1d4e89, #2a6fb0'/}
  //
  // The rows are a half radius apart, two of them - one laid along by a radius - a tile two
  // radii wide, as many rows high as the inks take to come back to the first on an even row.
  // Every scale that reaches into the tile is drawn there, the rows from the top down, as in
  // the picture as a whole: a scale cut by the edge of the tile is the one coming in on the
  // other side, under and over the same scales.

  return [

    'size'    => [ 20, 4, 300 ],
    'colors'  => [ '#f7f3ea', '#cfe0dc', '#b4cfc9' ],
    'options' => [ 'rings' => [ 3, 1, 8 ] ],

    'draw'    => function ( $p ) {

      $r     = $p ['size'];
      $rings = (int) round ( $p ['rings'] );
      $n     = count ( $p ['ink'] );
      $rows  = ( $n % 2 ) ? 2 * $n : $n;
      $line  = padPatternAttr ( $p ['bg'] );
      $width = padPatternNum ( max ( 0.5, $r / 14 ) );
      $body  = '';

      // The rows from two above the tile to two below it, from the top down: each scale a
      // disc and its rings, the scales of a row at the tile's left edge, its middle and its
      // right edge.

      for ( $j = -2; $j <= $rows + 2; $j++ ) {

        $y    = $j * $r / 2;
        $ink  = padPatternAttr ( $p ['ink'] [ ( $j % $n + $n ) % $n ] );
        $from = ( $j % 2 ) ? -1 : 0;

        for ( $i = $from; $i <= 3; $i += 2 ) {

          $x     = $i * $r;
          $at    = 'cx="' . padPatternNum ( $x ) . '" cy="' . padPatternNum ( $y ) . '"';
          $body .= "<circle $at r=\"" . padPatternNum ( $r ) . "\" fill=\"$ink\"/>";

          for ( $k = 1; $k < $rings; $k++ )
            $body .= "<circle $at r=\"" . padPatternNum ( $r * ( 1 - $k / $rings ) ) . '"/>';

        }

      }

      return [ "<pattern id=\"{$p ['id']}\" width=\"" . padPatternNum ( 2 * $r ) . '" height="' . padPatternNum ( $rows * $r / 2 ) . '" patternUnits="userSpaceOnUse">'
               . "<g fill=\"none\" stroke=\"$line\" stroke-width=\"$width\">$body</g></pattern>",
               padPatternFill ( $p, $p ['id'] ) ];

    }

  ];

?>
