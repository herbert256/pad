<?php

  // atkinson - Bill Atkinson's dither of the first Macintosh: like dither, but only three
  // quarters of each pixel's error carried on, to six neighbours - lighter, higher contrast;
  // levels= and colors= as dither's.
  //
  //   {img 'photos/harbour.jpg', width=400, filter='atkinson', colors='#222, #f4f1e8', alt='The harbour'/}

  return [

    'form'     => 'image',
    'loops'    => TRUE,
    'pixels'   => TRUE,
    'lossless' => TRUE,
    'options'  => [ 'levels' => [ 'number', 2, 2, 16 ], 'colors' => [ 'colors', [ '#000000', '#ffffff' ] ] ],

    'apply'    => function ( $image, $o, $scale ) {

      include_once PAD . 'subs/filters/_lib/dither.php';

      return padFilterDither ( $image, $o, 'atkinson' );

    }

  ];

?>
