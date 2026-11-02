<?php

  // maze - a perfect maze the seed digs: passages the size wide, every place joined to every
  // other by exactly one way, its walls in the first ink, entered at the top left and left at
  // the bottom right.
  //
  //   {pattern:maze size=16, seed=6, colors='#fdf6e3, #586e75'/}
  //
  // Dug by the recursive backtracker: from the top left square to a neighbour not yet visited,
  // picked by the seed, the wall between them knocked through, and back along the way dug when
  // a square has no such neighbour left - its long winding corridors the look of it. The
  // squares fill the picture, those cut by its right and bottom edge too; the walls left
  // standing are written as runs, a run of walls along a line one stroke.

  return [

    'size'    => [ 20, 4, 400 ],
    'colors'  => [ '#f6f4ef', '#b8b2a6' ],

    // The digging visits every square: 40,000 of them, some half a second.

    'count'   => [ 'squares', 40000, fn ( $p ) => (int) ceil ( $p ['w'] / $p ['size'] ) * (int) ceil ( $p ['h'] / $p ['size'] ),
                   'a larger size or a smaller width and height' ],

    'draw'    => function ( $p ) {

      $s    = $p ['size'];
      $cols = (int) ceil ( $p ['w'] / $s );
      $rows = (int) ceil ( $p ['h'] / $s );

      // $right [$j] [$i]: the wall right of square i, j stands; $below: the wall under it.

      $right = array_fill ( 0, $rows, array_fill ( 0, $cols, TRUE ) );
      $below = array_fill ( 0, $rows, array_fill ( 0, $cols, TRUE ) );
      $seen  = array_fill ( 0, $rows, array_fill ( 0, $cols, FALSE ) );
      $stack = [ [ 0, 0 ] ];

      $seen [0] [0] = TRUE;

      while ( $stack ) {

        list ( $i, $j ) = end ( $stack );

        $open = [];

        foreach ( [ [ 1, 0 ], [ -1, 0 ], [ 0, 1 ], [ 0, -1 ] ] as list ( $di, $dj ) )
          if ( $i + $di >= 0 and $i + $di < $cols and $j + $dj >= 0 and $j + $dj < $rows and ! $seen [ $j + $dj ] [ $i + $di ] )
            $open [] = [ $di, $dj ];

        if ( ! $open ) {
          array_pop ( $stack );
          continue;
        }

        list ( $di, $dj ) = $open [ padFakeNext () % count ( $open ) ];

        if ( $di == 1 )  $right [$j] [$i]     = FALSE;
        if ( $di == -1 ) $right [$j] [$i - 1] = FALSE;
        if ( $dj == 1 )  $below [$j] [$i]     = FALSE;
        if ( $dj == -1 ) $below [$j - 1] [$i] = FALSE;

        $seen [ $j + $dj ] [ $i + $di ] = TRUE;
        $stack [] = [ $i + $di, $j + $dj ];

      }

      // The walls as runs: across, the top edge - open over the first square - and the wall
      // under each row, the last open under the last square; down, the left edge and the wall
      // right of each column.

      $d   = '';
      $run = function ( $walls, $across, $at ) use ( &$d, $s ) {
        $from = NULL;
        foreach ( array_merge ( $walls, [ FALSE ] ) as $k => $wall ) {
          if ( $wall and $from === NULL )
            $from = $k;
          elseif ( ! $wall and $from !== NULL ) {
            $d   .= $across ? 'M' . padPatternNum ( $from * $s ) . ',' . padPatternNum ( $at ) . 'H' . padPatternNum ( $k * $s )
                            : 'M' . padPatternNum ( $at ) . ',' . padPatternNum ( $from * $s ) . 'V' . padPatternNum ( $k * $s );
            $from = NULL;
          }
        }
      };

      $top = array_fill ( 0, $cols, TRUE );
      $top [0] = FALSE;
      $run ( $top, TRUE, 0 );

      $below [ $rows - 1 ] [ $cols - 1 ] = FALSE;

      for ( $j = 0; $j < $rows; $j++ )
        $run ( $below [$j], TRUE, ( $j + 1 ) * $s );

      $run ( array_fill ( 0, $rows, TRUE ), FALSE, 0 );

      for ( $i = 0; $i < $cols; $i++ )
        $run ( array_column ( $right, $i ), FALSE, ( $i + 1 ) * $s );

      return [ '', "<path d=\"$d\" fill=\"none\" stroke=\"" . padPatternAttr ( $p ['ink'] [0] ) . '" stroke-width="' . padPatternNum ( max ( 1, $s / 6 ) )
                   . '" stroke-linecap="square"/>' ];

    }

  ];

?>
