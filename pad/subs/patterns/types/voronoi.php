<?php

  // voronoi - a Voronoi diagram: cells around points the seed scatters, one in every square of
  // the size, each cell in an ink the seed picks and the background as the seams between.
  //
  //   {pattern 'voronoi', size=50, seed=9/}
  //
  // A cell is the picture's rectangle cut by the half-plane of every neighbouring point - the
  // side of the line halfway between the two that is nearer to its own point. A point lies in
  // its own square, so the points two squares around it are all that can bound its cell.

  return [

    'size'    => [ 70, 16, 1000 ],
    'colors'  => [ '#ffffff', '#e63946', '#f1faee', '#a8dadc', '#457b9d', '#1d3557' ],

    'draw'    => function ( $p ) {

      include_once PAD . 'subs/patterns/_lib/voronoi.php';

      $w     = $p ['w'];
      $h     = $p ['h'];
      $cols  = max ( 1, (int) round ( $w / $p ['size'] ) );
      $rows  = max ( 1, (int) round ( $h / $p ['size'] ) );
      $cw    = $w / $cols;
      $ch    = $h / $rows;
      $seam  = max ( 1, $p ['size'] / 22 );
      $sites = [];

      for ( $j = 0; $j < $rows; $j++ )
        for ( $i = 0; $i < $cols; $i++ )
          $sites [$j] [$i] = [ ( $i + 0.1 + 0.8 * padPatternRand () ) * $cw, ( $j + 0.1 + 0.8 * padPatternRand () ) * $ch ];

      $paths = array_fill ( 0, count ( $p ['ink'] ), '' );

      for ( $j = 0; $j < $rows; $j++ )
        for ( $i = 0; $i < $cols; $i++ ) {

          list ( $px, $py ) = $sites [$j] [$i];

          $cell = [ [ - $seam, - $seam ], [ $w + $seam, - $seam ], [ $w + $seam, $h + $seam ], [ - $seam, $h + $seam ] ];

          for ( $b = max ( 0, $j - 2 ); $b <= min ( $rows - 1, $j + 2 ) and $cell; $b++ )
            for ( $a = max ( 0, $i - 2 ); $a <= min ( $cols - 1, $i + 2 ) and $cell; $a++ ) {
              if ( $a == $i and $b == $j )
                continue;
              list ( $qx, $qy ) = $sites [$b] [$a];
              $cell = padPatternClip ( $cell, ( $px + $qx ) / 2, ( $py + $qy ) / 2, $qx - $px, $qy - $py );
            }

          $pick = padFakeNext () % count ( $p ['ink'] );

          if ( count ( $cell ) < 3 )
            continue;

          $paths [$pick] .= 'M' . implode ( ' ', array_map ( fn ( $point ) => padPatternNum ( $point [0], 1 ) . ',' . padPatternNum ( $point [1], 1 ), $cell ) ) . 'Z';

        }

      $body = '<g stroke="' . padPatternAttr ( $p ['bg'] ) . '" stroke-width="' . padPatternNum ( $seam ) . '" stroke-linejoin="round">';

      foreach ( $paths as $i => $d )
        if ( $d !== '' )
          $body .= "<path d=\"$d\" fill=\"" . padPatternAttr ( $p ['ink'] [$i] ) . '"/>';

      return [ '', "$body</g>" ];

    }

  ];

?>
