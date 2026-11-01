<?php

  // sharpen - the edges of the picture crisper by amount= percent (50): every pixel minus the
  // pixels around it, a convolution of GD's.
  //
  //   {img 'photos/harbour.jpg', width=400, filter='sharpen', amount=80, alt='The harbour'/}
  //
  // The kernel is the picture plus amount times its Laplacian: 1 + 4a in the middle, -a on
  // the four sides, 0 on the corners - its sum 1, so the picture keeps its brightness.

  return [

    'form'    => 'image',
    'options' => [ 'amount' => [ 'number', 50, 0, 300 ] ],

    'apply'   => function ( $image, $o, $scale ) {

      $a = $o ['amount'] / 100;

      if ( $a > 0 )
        imageconvolution ( $image, [ [ 0, - $a, 0 ], [ - $a, 1 + 4 * $a, - $a ], [ 0, - $a, 0 ] ], 1, 0 );

      return $image;

    }

  ];

?>
