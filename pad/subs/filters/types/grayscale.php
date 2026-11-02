<?php

  // grayscale - the picture in shades of grey, each pixel its luminance: 0.299 of its red, 0.587
  // of its green and 0.114 of its blue, the weights of television (GD's grayscale filter).
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='grayscale', alt='The harbour'/}

  return [

    'form'  => 'image',

    'apply' => function ( $image, $o, $scale ) {

      imagefilter ( $image, IMG_FILTER_GRAYSCALE );

      return $image;

    }

  ];

?>
