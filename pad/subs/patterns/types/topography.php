<?php

  // topography - the contour lines of a landscape the seed makes up: value noise of three
  // octaves, its hills the size across, traced at levels= heights (12) by marching squares,
  // every fifth line the bolder index contour in the second ink.
  //
  //   {pattern 'topography', seed=7, levels=16/}
  //
  // The noise: a lattice of random heights a size apart, eased between its points (3t² - 2t³),
  // and two finer lattices of half and a quarter of that, at half and a quarter the weight,
  // spread over 0 to 1 by the lowest and the highest point sampled. The heights are sampled
  // on a grid of a twelfth of the size (3 to 12 pixels); each contour is the line of
  // marching squares through that grid, a saddle decided by the average of its four corners.
  // A line's points are the crossings on the edges of the grid, so two cells that share an
  // edge share its point exactly, and the pieces are joined into one path a line - closed
  // where the line closes, ending on the border where it leaves.

  return [

    'size'    => [ 90, 20, 1000 ],
    'colors'  => [ '#f6f1e7', '#c4b294', '#8c7356' ],
    'options' => [ 'levels' => [ 12, 2, 60 ] ],

    'draw'    => function ( $p ) {

      include_once PAD . 'subs/patterns/_lib/topography.php';

      $w    = $p ['w'];
      $h    = $p ['h'];
      $size = $p ['size'];
      $step = max ( 3, min ( 12, $size / 12 ) );
      $nx   = (int) ceil ( $w / $step );
      $ny   = (int) ceil ( $h / $step );

      // The three lattices, filled in this order - the seed's numbers, the same every time.

      $octaves = [];

      foreach ( [ 1, 2, 4 ] as $part ) {
        $cell = $size / $part;
        $gx   = (int) floor ( $w / $cell ) + 2;
        $gy   = (int) floor ( $h / $cell ) + 2;
        $grid = [];
        for ( $j = 0; $j <= $gy; $j++ )
          for ( $i = 0; $i <= $gx; $i++ )
            $grid [$j] [$i] = padPatternRand ();
        $octaves [] = [ $cell, 1 / $part, $grid ];
      }

      $field = [];
      $low   = INF;
      $high  = -INF;

      for ( $j = 0; $j <= $ny; $j++ )
        for ( $i = 0; $i <= $nx; $i++ ) {
          $value = 0;
          foreach ( $octaves as list ( $cell, $weight, $grid ) ) {
            $u  = $i * $step / $cell;
            $v  = $j * $step / $cell;
            $x0 = (int) floor ( $u );
            $y0 = (int) floor ( $v );
            $fx = $u - $x0;
            $fy = $v - $y0;
            $sx = $fx * $fx * ( 3 - 2 * $fx );
            $sy = $fy * $fy * ( 3 - 2 * $fy );
            $top    = $grid [$y0] [$x0]     + ( $grid [$y0] [$x0 + 1]     - $grid [$y0] [$x0] )     * $sx;
            $bottom = $grid [$y0 + 1] [$x0] + ( $grid [$y0 + 1] [$x0 + 1] - $grid [$y0 + 1] [$x0] ) * $sx;
            $value += $weight * ( $top + ( $bottom - $top ) * $sy );
          }
          $field [$j] [$i] = $value;
          $low  = min ( $low,  $value );
          $high = max ( $high, $value );
        }

      $range = ( $high > $low ) ? $high - $low : 1;

      for ( $j = 0; $j <= $ny; $j++ )
        for ( $i = 0; $i <= $nx; $i++ )
          $field [$j] [$i] = ( $field [$j] [$i] - $low ) / $range;

      $levels = (int) $p ['levels'];
      $thin   = '';
      $bold   = '';

      for ( $k = 0; $k < $levels; $k++ ) {
        $d = padPatternContour ( $field, $nx, $ny, $step, ( $k + 0.5 ) / $levels );
        if ( $k % 5 == 4 )
          $bold .= $d;
        else
          $thin .= $d;
      }

      $body = '<g fill="none" stroke-linejoin="round" stroke-linecap="round">'
            . ( $thin !== '' ? "<path d=\"$thin\" stroke=\"" . padPatternAttr ( $p ['ink'] [0] ) . '" stroke-width="1"/>' : '' )
            . ( $bold !== '' ? "<path d=\"$bold\" stroke=\"" . padPatternAttr ( $p ['ink'] [1] ?? $p ['ink'] [0] ) . '" stroke-width="1.8"/>' : '' )
            . '</g>';

      return [ '', $body ];

    }

  ];

?>
