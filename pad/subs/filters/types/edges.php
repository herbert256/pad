<?php

  // edges - the outlines of the picture: Sobel's operator, how fast its brightness changes
  // across and down every pixel, drawn from the first of colors= where nothing changes to the
  // second where the change is strongest - white lines on black, or '#fbfaf7, #1d1d1b' for a
  // drawing in ink on paper; amount= percent (100) makes the lines stronger or fainter.
  //
  //   {img 'photos/harbour.jpg', width=400, filter='edges', colors='#fbfaf7, #1d1d1b', alt='The harbour as a drawing'/}
  //
  // The brightness is the luminance of filter.php (Rec. 601, as grayscale). Each pixel's
  // change across is the right column of the 3 by 3 around it less the left one, weighed
  // 1 2 1, and down the bottom row less the top one; its strength the length of the two, a
  // quarter of it at amount=100 - so a hard step from black to white is the full second
  // colour, on the two pixels either side of it, and a picture of one colour has no edge
  // anywhere. Past the border the picture's own edge pixels are read again, so the frame of a
  // picture is no edge either. Written as a PNG unless format= says otherwise: thin lines of
  // flat colour, where JPEG rings.
  //
  // Transparency: the colour of a transparent pixel is no colour, so where the 3 by 3 reads one
  // it reads the brightness of the pixel in the middle instead - no line where a picture's
  // colour meets nothing - and the opacity has its own edges, the same way: the outline of a
  // logo on a transparent ground is a line, drawn on its own pixels, and every pixel keeps its
  // opacity, so the ground stays transparent - where reading a transparent pixel as black drew
  // a dark rim around every shape.

  return [

    'form'     => 'image',
    'loops'    => TRUE,
    'lossless' => TRUE,
    'options'  => [ 'colors' => [ 'colors', [ '#000000', '#ffffff' ], [ 'the ground first, then the lines', '#000, #fff', '#fbfaf7, #1d1d1b' ] ],
                    'amount' => [ 'number', 100, 10, 400 ] ],

    'apply'    => function ( $image, $o, $scale ) {

      $w      = imagesx ( $image );
      $h      = imagesy ( $image );
      $ground = padFilterRgb ( $o ['colors'] [0] );
      $line   = padFilterRgb ( $o ['colors'] [1] );
      $gain   = $o ['amount'] / 100 / 4;
      $ramp   = [];
      $values = [];
      $opaque = TRUE;

      for ( $v = 0; $v < 256; $v++ )
        $ramp [$v] = padFilterMix ( $ground, $line, $v / 255 );

      // Every pixel's luminance and its opacity, 0 to 255, as one number - the opacity times
      // 256 plus the luminance - a row a list: one list of the picture's size, where three
      // were 129 MB at 2 megapixels. A pixel that shows nothing has an opacity of 0.

      for ( $y = 0; $y < $h; $y++ )
        for ( $x = 0; $x < $w; $x++ ) {
          $c = imagecolorat ( $image, $x, $y );
          $t = ( $c >> 24 ) & 127;
          if ( $t )
            $opaque = FALSE;
          $values [$y] [$x] = padFilterLuma ( ( $c >> 16 ) & 255, ( $c >> 8 ) & 255, $c & 255 ) | ( intdiv ( ( 127 - $t ) * 255 + 63, 127 ) << 8 );
        }

      imagealphablending ( $image, FALSE );

      for ( $y = 0; $y < $h; $y++ ) {

        $a = $values [ max ( 0, $y - 1 ) ];
        $b = $values [$y];
        $c = $values [ min ( $h - 1, $y + 1 ) ];

        for ( $x = 0; $x < $w; $x++ ) {

          $left  = ( $x > 0 ) ? $x - 1 : 0;
          $right = ( $x < $w - 1 ) ? $x + 1 : $x;

          // Sobel, written out - a call a pixel was a third of the time. On a picture that
          // shows fully everywhere the opacity is the same 255 x 256 in every number, which
          // the weights, summing to 0, take out again: the sums of the numbers are those of
          // the luminances.

          if ( $opaque ) {
            $gx = ( $a [$right] + 2 * $b [$right] + $c [$right] ) - ( $a [$left] + 2 * $b [$left] + $c [$left] );
            $gy = ( $c [$left] + 2 * $c [$x] + $c [$right] ) - ( $a [$left] + 2 * $a [$x] + $a [$right] );
            $m  = sqrt ( $gx * $gx + $gy * $gy );
          } else {
            $mid = $b [$x] & 255;
            $p1  = $a [$left];  $p2 = $a [$x]; $p3 = $a [$right];
            $p4  = $b [$left];                 $p6 = $b [$right];
            $p7  = $c [$left];  $p8 = $c [$x]; $p9 = $c [$right];
            $l1  = ( $p1 >> 8 ) ? $p1 & 255 : $mid;
            $l2  = ( $p2 >> 8 ) ? $p2 & 255 : $mid;
            $l3  = ( $p3 >> 8 ) ? $p3 & 255 : $mid;
            $l4  = ( $p4 >> 8 ) ? $p4 & 255 : $mid;
            $l6  = ( $p6 >> 8 ) ? $p6 & 255 : $mid;
            $l7  = ( $p7 >> 8 ) ? $p7 & 255 : $mid;
            $l8  = ( $p8 >> 8 ) ? $p8 & 255 : $mid;
            $l9  = ( $p9 >> 8 ) ? $p9 & 255 : $mid;
            $gx  = ( $l3 + 2 * $l6 + $l9 ) - ( $l1 + 2 * $l4 + $l7 );
            $gy  = ( $l7 + 2 * $l8 + $l9 ) - ( $l1 + 2 * $l2 + $l3 );
            $ax  = ( ( $p3 >> 8 ) + 2 * ( $p6 >> 8 ) + ( $p9 >> 8 ) ) - ( ( $p1 >> 8 ) + 2 * ( $p4 >> 8 ) + ( $p7 >> 8 ) );
            $ay  = ( ( $p7 >> 8 ) + 2 * ( $p8 >> 8 ) + ( $p9 >> 8 ) ) - ( ( $p1 >> 8 ) + 2 * ( $p2 >> 8 ) + ( $p3 >> 8 ) );
            $m   = sqrt ( max ( $gx * $gx + $gy * $gy, $ax * $ax + $ay * $ay ) );
          }

          imagesetpixel ( $image, $x, $y, ( imagecolorat ( $image, $x, $y ) & 0x7F000000 ) | $ramp [ min ( 255, (int) round ( $m * $gain ) ) ] );

        }

      }

      return $image;

    }

  ];

?>
