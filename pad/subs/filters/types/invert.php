<?php

  // invert - the negative of the picture, every channel 255 less itself (GD's negate filter).
  //
  //   {img 'photos/harbour.jpg', width=400, filter='invert', alt='The harbour as a negative'/}

  return [

    'form'  => 'image',

    'apply' => function ( $image, $o, $scale ) {

      imagefilter ( $image, IMG_FILTER_NEGATE );

      return $image;

    }

  ];

?>
