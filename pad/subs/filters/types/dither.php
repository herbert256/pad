<?php

  // dither - Floyd and Steinberg's error diffusion: the picture in levels= greys (2, ink and
  // paper alone) from the first of colors= to the second, every pixel's error carried to its
  // neighbours not yet drawn; made at 1x, its pixels sharp, written as a PNG.
  //
  //   {pad:img 'photos/harbour.jpg', width=400, filter='dither', alt='The harbour'/}

  return [

    'form'     => 'image',
    'loops'    => TRUE,
    'pixels'   => TRUE,
    'lossless' => TRUE,
    'options'  => [ 'levels' => [ 'number', 2, 2, 16, 'whole' ], 'colors' => [ 'colors', [ '#000000', '#ffffff' ] ] ],

    'apply'    => function ( $image, $o, $scale ) {

      include_once PAD . 'subs/filters/_lib/dither.php';

      return padFilterDither ( $image, $o, 'floyd' );

    }

  ];

?>
