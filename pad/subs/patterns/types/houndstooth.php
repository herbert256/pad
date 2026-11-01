<?php

  // houndstooth - the broken check of the woollen cloth, the size its repeat: woven as the
  // cloth is, four threads of the ink and four of the background each way in a two-over-two
  // twill, so every crossing shows the thread on top - the jagged checks and their teeth. A
  // tile repeated by a <pattern>: the seed changes nothing.
  //
  //   {pattern 'houndstooth', size=16, colors='#f5f1e8, #1d1d1b'/}
  //
  // The tile is eight threads square, each a cell: the warp - the thread running down - is the
  // ink in its first four columns, the weft - running across - in its first four rows, and at
  // column x and row y the warp lies on top where (x + y) mod 4 is 0 or 1, the weft where it
  // is 2 or 3. A cell of the ink is a square of the path, the squares of a row joined into one
  // run.

  return [

    'size'    => [ 24, 4, 400 ],
    'colors'  => [ '#f4f1ea', '#3d3a35' ],

    'draw'    => function ( $p ) {

      $u = $p ['size'] / 8;
      $d = '';

      for ( $y = 0; $y < 8; $y++ ) {
        $from = NULL;
        for ( $x = 0; $x <= 8; $x++ ) {
          $dark = $x < 8 && ( ( ( $x + $y ) % 4 < 2 ) ? $x < 4 : $y < 4 );
          if ( $dark and $from === NULL )
            $from = $x;
          elseif ( ! $dark and $from !== NULL ) {
            $d   .= 'M' . padPatternNum ( $from * $u ) . ',' . padPatternNum ( $y * $u ) . 'h' . padPatternNum ( ( $x - $from ) * $u )
                  . 'v' . padPatternNum ( $u ) . 'h-' . padPatternNum ( ( $x - $from ) * $u ) . 'z';
            $from = NULL;
          }
        }
      }

      $side = padPatternNum ( $p ['size'] );

      return [ "<pattern id=\"{$p ['id']}\" width=\"$side\" height=\"$side\" patternUnits=\"userSpaceOnUse\">"
               . "<path d=\"$d\" fill=\"" . padPatternAttr ( $p ['ink'] [0] ) . '" shape-rendering="crispEdges"/></pattern>',
               padPatternFill ( $p, $p ['id'] ) ];

    }

  ];

?>
