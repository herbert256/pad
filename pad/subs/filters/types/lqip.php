<?php

  // lqip - a placeholder: a low quality image of the picture, 16 pixels on its longer side and
  // blurred, as a PNG in the page behind the <img> until the picture has loaded.
  //
  //   {pad:img 'photos/harbour.jpg', width=400, filter='lqip', alt='The harbour'/}
  //
  // The 1x picture as shown - every image filter of the list applied, wherever the placeholder
  // stands in it - made small, each pixel the average of what it covers, and blurred once by
  // GD's Gaussian blur: a few hundred bytes in the page, drawn over the whole box
  // (background-size: cover). A picture with transparent parts is laid on white first, as a
  // JPEG thumbnail is: they were black.

  return [

    'form'       => 'placeholder',
    'background' => TRUE,

    'hold'       => function ( $image, $o ) {

      $w     = imagesx ( $image );
      $h     = imagesy ( $image );
      $small = padFilterResize ( padFilterFlat ( $image, 0xffffff ), max ( 1, (int) round ( $w * 16 / max ( $w, $h ) ) ), max ( 1, (int) round ( $h * 16 / max ( $w, $h ) ) ) );

      imagefilter ( $small, IMG_FILTER_GAUSSIAN_BLUR );
      imagesavealpha ( $small, FALSE );

      ob_start ();
      imagepng ( $small, NULL, 9 );
      $png = ob_get_clean ();

      return [ 'style' => [ 'background-image' => 'url(data:image/png;base64,' . base64_encode ( $png ) . ')', 'background-size' => 'cover' ] ];

    }

  ];

?>
