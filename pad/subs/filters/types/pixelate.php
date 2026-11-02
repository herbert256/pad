<?php

  // pixelate - the picture in blocks of cell= pixels (8), each block the average of what it
  // covers (GD's pixelate filter, its advanced form); made at 1x and shown with its pixels
  // sharp, written as a PNG unless format= says otherwise.
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='pixelate', cell=12, alt='The harbour'/}

  return [

    'form'     => 'image',
    'pixels'   => TRUE,
    'lossless' => TRUE,
    'options'  => [ 'cell' => [ 'number', 8, 2, 100, 'whole' ] ],

    'apply'    => function ( $image, $o, $scale ) {

      imagefilter ( $image, IMG_FILTER_PIXELATE, (int) $o ['cell'], TRUE );

      return $image;

    }

  ];

?>
