<?php

  // solarize - the Sabattier effect of the darkroom, a print flashed with light while it
  // develops: every channel at threshold= percent (50) of its full value or more turned into
  // its negative, 255 less itself, the rest left as it was - the highlights go dark and strange,
  // the shadows stay.
  //
  //   {image:thumbnail 'photos/harbour.jpg', width=400, filter='solarize', threshold=40, alt='The harbour, solarized'/}
  //
  // Each of red, green and blue on its own, as PIL's ImageOps.solarize and ImageMagick's
  // -solarize do it: the cut is threshold x 255 / 100, so at 50 a channel of 128 or more turns
  // and one of 127 or less stays - PIL's default of 128, and ImageMagick's 50% - at 0 the whole
  // picture is its negative, as invert, and at 100 only a channel of 255 turns, to 0. Every
  // pixel keeps its opacity.

  return [

    'form'    => 'image',
    'loops'   => TRUE,
    'options' => [ 'threshold' => [ 'number', 50, 0, 100 ] ],

    'apply'   => function ( $image, $o, $scale ) {

      $cut  = $o ['threshold'] * 255 / 100;
      $turn = [];

      for ( $v = 0; $v < 256; $v++ )
        $turn [$v] = ( $v >= $cut ) ? 255 - $v : $v;

      return padFilterMap ( $image, fn ( $r, $g, $b ) => ( $turn [$r] << 16 ) | ( $turn [$g] << 8 ) | $turn [$b] );

    }

  ];

?>
