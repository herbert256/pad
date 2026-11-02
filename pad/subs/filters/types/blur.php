<?php

  // blur - the picture out of focus by radius= CSS pixels (6), the same on the 2x picture.
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='blur', radius=10, alt='The harbour'/}
  //
  // Up to 2 pixels GD's 3 by 3 Gaussian blur runs once a pixel; beyond that the picture is
  // made smaller by half the radius - each pixel the average of what it covered - and large
  // again, and the Gaussian blur smooths what is left of the steps twice: a wide blur at the
  // cost of a small one.
  //
  // A picture with transparent parts - a PNG, GIF or WebP logo - keeps them, blurred as its
  // colours are: GD's Gaussian blur mixes the colours of a pixel's neighbours whatever their
  // opacity, and the black of a transparent pixel darkened the edge beside it. So such a
  // picture is blurred twice, as two pictures without transparency - its colours laid on black,
  // each weighed by its opacity, and its opacity as a grey picture - and every pixel is its
  // blurred colour over its blurred opacity, at that opacity: a pixel by pixel pass, of a tenth
  // of a microsecond each, a picture that shows fully everywhere does not take.

  return [

    'form'    => 'image',
    'options' => [ 'radius' => [ 'number', 6, 1, 50 ] ],

    'apply'   => function ( $image, $o, $scale ) {

      $radius = $o ['radius'] * $scale;
      $w      = imagesx ( $image );
      $h      = imagesy ( $image );

      $blur = function ( $picture ) use ( $radius, $w, $h ) {

        imagealphablending ( $picture, FALSE );

        if ( $radius <= 2 ) {
          for ( $k = 0; $k < round ( $radius ); $k++ )
            imagefilter ( $picture, IMG_FILTER_GAUSSIAN_BLUR );
          return $picture;
        }

        $small = padFilterResize ( $picture, (int) round ( $w * 2 / $radius ), (int) round ( $h * 2 / $radius ) );

        imagecopyresampled ( $picture, $small, 0, 0, 0, 0, $w, $h, imagesx ( $small ), imagesy ( $small ) );
        imagefilter ( $picture, IMG_FILTER_GAUSSIAN_BLUR );
        imagefilter ( $picture, IMG_FILTER_GAUSSIAN_BLUR );

        return $picture;

      };

      $opacity = padFilterOpacity ( $image );

      if ( $opacity === NULL )
        return $blur ( $image );

      $colour = imagecreatetruecolor ( $w, $h );

      imagecopy ( $colour, $image, 0, 0, 0, 0, $w, $h );

      $blur ( $colour );
      $blur ( $opacity );

      imagesavealpha ( $colour, TRUE );

      for ( $y = 0; $y < $h; $y++ )
        for ( $x = 0; $x < $w; $x++ ) {
          $m = imagecolorat ( $opacity, $x, $y ) & 255;
          if ( $m == 255 )
            continue;
          if ( $m == 0 ) {
            imagesetpixel ( $colour, $x, $y, 0x7F000000 );
            continue;
          }
          $c = imagecolorat ( $colour, $x, $y );
          imagesetpixel ( $colour, $x, $y, ( ( 127 - intdiv ( $m * 127 + 127, 255 ) ) << 24 )
                                         | ( min ( 255, intdiv ( ( ( $c >> 16 ) & 255 ) * 255 + ( $m >> 1 ), $m ) ) << 16 )
                                         | ( min ( 255, intdiv ( ( ( $c >> 8 ) & 255 ) * 255 + ( $m >> 1 ), $m ) ) << 8 )
                                         |   min ( 255, intdiv ( ( $c & 255 ) * 255 + ( $m >> 1 ), $m ) ) );
        }

      return $colour;

    }

  ];

?>
