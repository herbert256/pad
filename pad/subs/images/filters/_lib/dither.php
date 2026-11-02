<?php

  // The dithers of the filter subsystem - dither (Floyd and Steinberg), atkinson and bayer
  // (types/): the picture's luminance cut to levels= greys (2: ink and paper alone) and the
  // error of every cut carried to the pixels not yet cut, or for bayer weighed against a
  // threshold that changes from pixel to pixel - so a grey becomes a pattern of the levels
  // that has that grey on average. The levels are drawn from the first of colors= (the
  // darkest) to the second (the lightest), black and white unless said otherwise.
  //
  // padFilterDither  the picture dithered by one of the three ways

  function padFilterDither ( $image, $o, $way ) {

    $w      = imagesx ( $image );
    $h      = imagesy ( $image );
    $levels = (int) $o ['levels'] - 1;
    $lumas  = padFilterLumas ( $image );
    $ink    = padFilterRgb ( $o ['colors'] [0] );
    $paper  = padFilterRgb ( $o ['colors'] [1] );
    $shades = [];

    for ( $k = 0; $k <= $levels; $k++ )
      $shades [$k] = padFilterMix ( $ink, $paper, $k / $levels );

    // Bayer's threshold map of 8 by 8, each cell a number from 0 to 63, built from the map of
    // 2 by 2 - 0 2 / 3 1 - four times over.

    $map = [ [ 0 ] ];

    if ( $way == 'bayer' )
      for ( $size = 1; $size < 8; $size *= 2 ) {
        $next = [];
        foreach ( [ [ 0, 0, 0 ], [ 0, 1, 2 ], [ 1, 0, 3 ], [ 1, 1, 1 ] ] as list ( $dy, $dx, $add ) )
          for ( $y = 0; $y < $size; $y++ )
            for ( $x = 0; $x < $size; $x++ )
              $next [ $dy * $size + $y ] [ $dx * $size + $x ] = 4 * $map [$y] [$x] + $add;
        $map = $next;
      }

    imagealphablending ( $image, FALSE );

    for ( $y = 0; $y < $h; $y++ )
      for ( $x = 0; $x < $w; $x++ ) {

        $value = $lumas [$y] [$x];

        if ( $way == 'bayer' )
          $level = (int) floor ( max ( 0, min ( 255, $value ) ) * $levels / 255 + ( $map [ $y % 8 ] [ $x % 8 ] + 0.5 ) / 64 );
        else
          $level = (int) round ( max ( 0, min ( 255, $value ) ) * $levels / 255 );

        $level = max ( 0, min ( $levels, $level ) );

        if ( $way != 'bayer' ) {

          $error = $value - $level * 255 / $levels;

          // Floyd and Steinberg: 7/16 to the right, 3/16 below left, 5/16 below, 1/16 below
          // right. Atkinson: an eighth each to the two on the right, the three below and the
          // one two below - six eighths, the rest of the error let go.

          $spread = ( $way == 'atkinson' )
            ? [ [ 1, 0, 1 / 8 ], [ 2, 0, 1 / 8 ], [ -1, 1, 1 / 8 ], [ 0, 1, 1 / 8 ], [ 1, 1, 1 / 8 ], [ 0, 2, 1 / 8 ] ]
            : [ [ 1, 0, 7 / 16 ], [ -1, 1, 3 / 16 ], [ 0, 1, 5 / 16 ], [ 1, 1, 1 / 16 ] ];

          foreach ( $spread as list ( $dx, $dy, $part ) )
            if ( isset ( $lumas [ $y + $dy ] [ $x + $dx ] ) )
              $lumas [ $y + $dy ] [ $x + $dx ] += $error * $part;

        }

        $c = imagecolorat ( $image, $x, $y );
        imagesetpixel ( $image, $x, $y, ( $c & 0x7F000000 ) | $shades [$level] );

      }

    return $image;

  }

?>
