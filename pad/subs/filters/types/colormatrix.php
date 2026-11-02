<?php
  // colormatrix - transform normalized RGBA channels with four rows of five coefficients.
  // Each row multiplies red, green, blue, alpha and the constant 1; this swaps red and blue.
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='colormatrix', matrix='0,0,1,0,0, 0,1,0,0,0, 1,0,0,0,0, 0,0,0,1,0', alt='The harbour with red and blue swapped'/}
  //
  return [ 'form' => 'image', 'loops' => TRUE,
    'options' => [ 'matrix' => [ 'matrix', [1,0,0,0,0, 0,1,0,0,0, 0,0,1,0,0, 0,0,0,1,0], 20, -10, 10 ] ],
    'apply' => function ( $image, $options, $scale ) {
      $matrix = $options ['matrix']; imagealphablending ( $image, FALSE ); imagesavealpha ( $image, TRUE );
      for ( $y = 0; $y < imagesy ( $image ); $y++ )
        for ( $x = 0; $x < imagesx ( $image ); $x++ ) {
          $pixel = imagecolorat ( $image, $x, $y );
          $input = [ ( ( $pixel >> 16 ) & 255 ) / 255, ( ( $pixel >> 8 ) & 255 ) / 255, ( $pixel & 255 ) / 255, 1 - ( ( $pixel >> 24 ) & 127 ) / 127, 1 ];
          $output = [];
          for ( $row = 0; $row < 4; $row++ ) {
            $sum = 0;
            for ( $column = 0; $column < 5; $column++ ) $sum += $matrix [ $row * 5 + $column ] * $input [$column];
            $output [] = max ( 0, min ( 1, $sum ) );
          }
          imagesetpixel ( $image, $x, $y, ( (int) round ( 127 * ( 1 - $output [3] ) ) << 24 )
            | ( (int) round ( 255 * $output [0] ) << 16 ) | ( (int) round ( 255 * $output [1] ) << 8 ) | (int) round ( 255 * $output [2] ) );
        }
      return $image;
    }
  ];
?>
