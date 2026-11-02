<?php
  // Four rows of five coefficients, normalized unpremultiplied RGBA plus constant 1.
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
