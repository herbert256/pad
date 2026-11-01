<?php

  // bayer - ordered dithering: every pixel weighed against Bayer's threshold map of 8 by 8, the
  // cross-hatch of an old screen; levels= and colors= as dither's.
  //
  //   {img 'photos/harbour.jpg', width=400, filter='bayer', levels=4, alt='The harbour'/}

  return [

    'form'     => 'image',
    'pixels'   => TRUE,
    'lossless' => TRUE,
    'options'  => [ 'levels' => [ 'number', 2, 2, 16 ], 'colors' => [ 'colors', [ '#000000', '#ffffff' ] ] ],

    'apply'    => function ( $image, $o, $scale ) {

      include_once PAD . 'subs/filters/_lib/dither.php';

      return padFilterDither ( $image, $o, 'bayer' );

    }

  ];

?>
