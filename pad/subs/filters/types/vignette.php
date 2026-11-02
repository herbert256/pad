<?php

  // vignette - the corners of the picture darker, as an old lens draws them, by amount= percent
  // (50) at the very corner, the middle untouched.
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='vignette', amount=70, alt='The harbour'/}
  //
  // A pixel's distance from the middle is measured in halves of the picture's width and height,
  // 1 at a corner; from 0.35 of the way out it darkens along a smoothstep to the amount.

  return [

    'form'    => 'image',
    'loops'   => TRUE,
    'options' => [ 'amount' => [ 'number', 50, 0, 100 ] ],

    'apply'   => function ( $image, $o, $scale ) {

      $w = imagesx ( $image );
      $h = imagesy ( $image );
      $a = $o ['amount'] / 100;

      imagealphablending ( $image, FALSE );

      for ( $y = 0; $y < $h; $y++ ) {
        $dy = ( 2 * ( $y + 0.5 ) / $h - 1 ) ** 2;
        for ( $x = 0; $x < $w; $x++ ) {
          $d = sqrt ( ( ( 2 * ( $x + 0.5 ) / $w - 1 ) ** 2 + $dy ) / 2 );
          if ( $d <= 0.35 )
            continue;
          $t = min ( 1, ( $d - 0.35 ) / 0.65 );
          $f = 1 - $a * $t * $t * ( 3 - 2 * $t );
          $c = imagecolorat ( $image, $x, $y );
          imagesetpixel ( $image, $x, $y, ( $c & 0x7F000000 ) | ( (int) round ( ( ( $c >> 16 ) & 255 ) * $f ) << 16 )
                                          | ( (int) round ( ( ( $c >> 8 ) & 255 ) * $f ) << 8 ) | (int) round ( ( $c & 255 ) * $f ) );
        }
      }

      return $image;

    }

  ];

?>
