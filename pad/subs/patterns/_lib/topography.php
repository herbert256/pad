<?php

  // The contour lines of the topography pattern (types/topography.php): marching squares over
  // a grid of heights from 0 to 1, and the pieces joined into lines.
  //
  // padPatternContour  the path data of the line at height $t - one subpath a line, closed
  //                    with Z where it closes; '' when the height is nowhere crossed
  //
  // A cell of the grid has its four corners above or below $t - one of sixteen cases - and
  // the line crosses the edges between a corner above and one below, at the point found by
  // linear interpolation along that edge. The crossing is named by its edge: the edge from
  // (i, j) to (i + 1, j) is 2n, the one from (i, j) to (i, j + 1) 2n + 1, n = j * (nx + 1) + i
  // - so the two cells that share an edge name the same crossing, computed once, and the
  // pieces are chained by those names. A saddle - two opposite corners above, two below - is
  // decided by the average of the four: above, the corners above are joined through the
  // middle. Every name is crossed by at most two pieces, one of each cell beside it: a line is
  // followed from a name crossed once - an end on the border - and what is left are loops.

  function padPatternContour ( $f, $nx, $ny, $step, $t ) {

    $pieces = [];
    $row    = $nx + 1;

    for ( $j = 0; $j < $ny; $j++ )
      for ( $i = 0; $i < $nx; $i++ ) {

        $a = $f [$j] [$i];
        $b = $f [$j] [$i + 1];
        $c = $f [$j + 1] [$i + 1];
        $d = $f [$j + 1] [$i];

        $case = ( $a > $t ? 8 : 0 ) | ( $b > $t ? 4 : 0 ) | ( $c > $t ? 2 : 0 ) | ( $d > $t ? 1 : 0 );

        if ( $case == 0 or $case == 15 )
          continue;

        $top    = ( $j * $row + $i ) * 2;
        $bottom = ( ( $j + 1 ) * $row + $i ) * 2;
        $left   = ( $j * $row + $i ) * 2 + 1;
        $right  = ( $j * $row + $i + 1 ) * 2 + 1;
        $middle = ( $a + $b + $c + $d ) / 4 > $t;

        switch ( $case ) {
          case 1: case 14: $pieces [] = [ $left, $bottom ]; break;
          case 2: case 13: $pieces [] = [ $bottom, $right ]; break;
          case 3: case 12: $pieces [] = [ $left, $right ]; break;
          case 4: case 11: $pieces [] = [ $top, $right ]; break;
          case 6: case 9:  $pieces [] = [ $top, $bottom ]; break;
          case 7: case 8:  $pieces [] = [ $left, $top ]; break;
          case 5:
            if ( $middle ) { $pieces [] = [ $left, $top ]; $pieces [] = [ $bottom, $right ]; }
            else           { $pieces [] = [ $top, $right ]; $pieces [] = [ $left, $bottom ]; }
            break;
          case 10:
            if ( $middle ) { $pieces [] = [ $top, $right ]; $pieces [] = [ $left, $bottom ]; }
            else           { $pieces [] = [ $left, $top ]; $pieces [] = [ $bottom, $right ]; }
            break;
        }

      }

    if ( ! $pieces )
      return '';

    $by = [];

    foreach ( $pieces as $n => list ( $one, $two ) ) {
      $by [$one] [] = $n;
      $by [$two] [] = $n;
    }

    // The ends first, in the order the grid found them, then the loops.

    $starts = [];

    foreach ( $by as $name => $list )
      if ( count ( $list ) == 1 )
        $starts [] = $name;

    foreach ( $by as $name => $list )
      if ( count ( $list ) > 1 )
        $starts [] = $name;

    $used   = [];
    $points = [];
    $path   = '';

    $point = function ( $name ) use ( $f, $row, $step, $t, &$points ) {
      if ( ! isset ( $points [$name] ) ) {
        $edge = $name >> 1;
        $i    = $edge % $row;
        $j    = intdiv ( $edge, $row );
        $v1   = $f [$j] [$i];
        if ( $name & 1 ) {
          $v2 = $f [$j + 1] [$i];
          $x  = $i * $step;
          $y  = ( $j + ( $t - $v1 ) / ( $v2 - $v1 ) ) * $step;
        } else {
          $v2 = $f [$j] [$i + 1];
          $x  = ( $i + ( $t - $v1 ) / ( $v2 - $v1 ) ) * $step;
          $y  = $j * $step;
        }
        $points [$name] = padPatternNum ( $x, 1 ) . ',' . padPatternNum ( $y, 1 );
      }
      return $points [$name];
    };

    foreach ( $starts as $start ) {

      $line   = [ $start ];
      $at     = $start;
      $closed = FALSE;

      while ( TRUE ) {
        $next = NULL;
        foreach ( $by [$at] as $n )
          if ( ! isset ( $used [$n] ) ) {
            $next = $n;
            break;
          }
        if ( $next === NULL )
          break;
        $used [$next] = TRUE;
        $at = ( $pieces [$next] [0] == $at ) ? $pieces [$next] [1] : $pieces [$next] [0];
        if ( $at == $start ) {
          $closed = TRUE;
          break;
        }
        $line [] = $at;
      }

      if ( count ( $line ) < 2 )
        continue;

      $path .= 'M' . implode ( ' ', array_map ( $point, $line ) ) . ( $closed ? 'Z' : '' );

    }

    return $path;

  }

?>
