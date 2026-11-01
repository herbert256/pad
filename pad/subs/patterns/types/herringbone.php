<?php

  // herringbone - parquet blocks laid in a herringbone: the size their width, ratio= times as
  // long (3), the blocks that lie in the first ink and the blocks that stand in the second, each
  // block's end against the side of the next, turned by angle= (45 degrees: the zigzag runs up
  // the picture; 0 lays it as a staircase). A tile repeated by a <pattern>: the seed changes
  // nothing.
  //
  //   {pattern 'herringbone', size=10, ratio=4, colors='#3b2f2f, #c8a27a, #a47551'/}
  //
  // In units of the size, with r the ratio, the k-th block lying is [k, k+r] by [k, k+1] and
  // the k-th standing [k+r, k+r+1] by [k+1-r, k+1]: a staircase that goes on by (1, 1), the
  // next staircase the same 2r further along - so a square of 2r is the tile, and every block
  // that reaches into it is drawn there, cut by its edges, each set in by half the joint (an
  // eighth of the size) on every side. A block cut by the edge of the tile is drawn whole in
  // the tiles on either side, so the halves meet exactly. The ratio is a whole number.

  return [

    'size'    => [ 10, 2, 200 ],
    'colors'  => [ '#efe7da', '#e0cfb5', '#d3bf9f' ],
    'options' => [ 'ratio' => [ 3, 2, 8 ],
                   'angle' => [ 45, -360, 360 ] ],

    'draw'    => function ( $p ) {

      $u     = $p ['size'];
      $r     = (int) round ( $p ['ratio'] );
      $tile  = 2 * $r;
      $joint = $u / 8;
      $paths = [ '', '' ];

      // Every block of the staircases through the tile: k along the staircase, m the
      // staircases side by side - those that miss the tile are left out.

      for ( $m = -1; $m <= 1; $m++ )
        for ( $k = - $tile - $r; $k <= $tile + $r; $k++ ) {
          $x = $k + $m * $tile;
          foreach ( [ [ 0, $x, $k, $r, 1 ], [ 1, $x + $r, $k + 1 - $r, 1, $r ] ] as list ( $way, $bx, $by, $bw, $bh ) ) {
            if ( $bx >= $tile or $by >= $tile or $bx + $bw <= 0 or $by + $bh <= 0 )
              continue;
            $paths [$way] .= 'M' . padPatternNum ( $bx * $u + $joint / 2 ) . ',' . padPatternNum ( $by * $u + $joint / 2 )
                           . 'h' . padPatternNum ( $bw * $u - $joint ) . 'v' . padPatternNum ( $bh * $u - $joint )
                           . 'h-' . padPatternNum ( $bw * $u - $joint ) . 'z';
          }
        }

      $side  = padPatternNum ( $tile * $u );
      $angle = fmod ( $p ['angle'], 360 );
      $turn  = ( $angle != 0 ) ? ' patternTransform="rotate(' . padPatternNum ( $angle ) . ')"' : '';

      return [ "<pattern id=\"{$p ['id']}\" width=\"$side\" height=\"$side\" patternUnits=\"userSpaceOnUse\"$turn>"
               . "<path d=\"{$paths [0]}\" fill=\"" . padPatternAttr ( $p ['ink'] [0] ) . '"/>'
               . "<path d=\"{$paths [1]}\" fill=\"" . padPatternAttr ( $p ['ink'] [1] ?? $p ['ink'] [0] ) . '"/>'
               . '</pattern>',
               padPatternFill ( $p, $p ['id'] ) ];

    }

  ];

?>
