<?php

  // threshold - the picture in two colours alone, a hard cut: every pixel whose luminance is
  // threshold= percent (50) of white or more the second of colors= (paper), every other one the
  // first (ink) - a stencil, a stamp, a scanned page made clean, where dither spreads the error
  // on to keep the greys.
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='threshold', threshold=60, colors='#1d1d1b, #f4f1e8', alt='The harbour'/}
  //
  // The luminance is that of filter.php (Rec. 601, 0 to 255, as grayscale), the cut threshold
  // x 255 / 100: at 50 the pixels of 128 and more are paper and those of 127 and less ink; at 0
  // everything is paper, at 100 only white is. Every pixel keeps its opacity: the transparent
  // ground of a logo stays transparent, a half transparent edge is ink or paper at half. Two
  // colours: a PNG unless format= says otherwise, a few kilobytes.

  return [

    'form'     => 'image',
    'loops'    => TRUE,
    'lossless' => TRUE,
    'options'  => [ 'threshold' => [ 'number', 50, 0, 100 ], 'colors' => [ 'colors', [ '#000000', '#ffffff' ] ] ],

    'apply'    => function ( $image, $o, $scale ) {

      $cut   = $o ['threshold'] * 255 / 100;
      $rgb   = fn ( $c ) => ( $c [0] << 16 ) | ( $c [1] << 8 ) | $c [2];
      $ink   = $rgb ( padFilterRgb ( $o ['colors'] [0] ) );
      $paper = $rgb ( padFilterRgb ( $o ['colors'] [1] ) );

      return padFilterMap ( $image, fn ( $r, $g, $b ) => ( padFilterLuma ( $r, $g, $b ) >= $cut ) ? $paper : $ink );

    }

  ];

?>
