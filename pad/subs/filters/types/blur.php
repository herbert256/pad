<?php

  // blur - the picture out of focus by radius= CSS pixels (6), the same on the 2x picture.
  //
  //   {img 'photos/harbour.jpg', width=400, filter='blur', radius=10, alt='The harbour'/}
  //
  // Up to 2 pixels GD's 3 by 3 Gaussian blur runs once a pixel; beyond that the picture is
  // made smaller by half the radius - each pixel the average of what it covered - and large
  // again, and the Gaussian blur smooths what is left of the steps twice: a wide blur at the
  // cost of a small one.

  return [

    'form'    => 'image',
    'options' => [ 'radius' => [ 'number', 6, 1, 50 ] ],

    'apply'   => function ( $image, $o, $scale ) {

      $radius = $o ['radius'] * $scale;

      if ( $radius <= 2 ) {
        for ( $k = 0; $k < round ( $radius ); $k++ )
          imagefilter ( $image, IMG_FILTER_GAUSSIAN_BLUR );
        return $image;
      }

      $w     = imagesx ( $image );
      $h     = imagesy ( $image );
      $small = padFilterResize ( $image, (int) round ( $w * 2 / $radius ), (int) round ( $h * 2 / $radius ) );

      imagecopyresampled ( $image, $small, 0, 0, 0, 0, $w, $h, imagesx ( $small ), imagesy ( $small ) );
      imagefilter ( $image, IMG_FILTER_GAUSSIAN_BLUR );
      imagefilter ( $image, IMG_FILTER_GAUSSIAN_BLUR );

      return $image;

    }

  ];

?>
