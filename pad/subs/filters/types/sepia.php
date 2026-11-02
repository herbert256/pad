<?php

  // sepia - the brown of an old photograph: every pixel through the classic sepia matrix, amount=
  // percent of the way from the picture to it (100).
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='sepia', amount=80, alt='The harbour'/}
  //
  // The matrix: red 0.393 0.769 0.189, green 0.349 0.686 0.168, blue 0.272 0.534 0.131 of the
  // red, green and blue, each at most 255.

  return [

    'form'    => 'image',
    'loops'   => TRUE,
    'options' => [ 'amount' => [ 'number', 100, 0, 100 ] ],

    'apply'   => function ( $image, $o, $scale ) {

      $t = $o ['amount'] / 100;

      return padFilterMap ( $image, function ( $r, $g, $b ) use ( $t ) {
        $sr = min ( 255, 0.393 * $r + 0.769 * $g + 0.189 * $b );
        $sg = min ( 255, 0.349 * $r + 0.686 * $g + 0.168 * $b );
        $sb = min ( 255, 0.272 * $r + 0.534 * $g + 0.131 * $b );
        return ( (int) round ( $r + ( $sr - $r ) * $t ) << 16 ) | ( (int) round ( $g + ( $sg - $g ) * $t ) << 8 ) | (int) round ( $b + ( $sb - $b ) * $t );
      } );

    }

  ];

?>
