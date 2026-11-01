<?php

  // guilloche - the engraved lines of a certificate or a banknote: bands the size high, each
  // woven of lines= copies of one wave (12), the copies set a step apart along it and every
  // second one turned upside down, so they cross and interlace into lenses and braids. The wave
  // is two sines, the second two or three times as fast; their lengths, weights and phase are
  // the seed's. The inks take turns line by line. Decoration only - no security printing.
  //
  //   {pattern 'guilloche', size=70, lines=16, seed=4, colors='#fbf8f1, #2c6e63, #b08d57'/}
  //
  // The wave reaches 0.6 of the size either way, so the bands beside each other interlace too.
  // It is drawn once, a polyline from a wavelength left of the picture to its right edge, its
  // points a twelfth of the faster sine's wavelength apart and 3 pixels at most; a band is that
  // wave used again - a <use> - a line at a time, the upside-down ones mirrored about the band's
  // middle, and every band a <use> of the first: the SVG grows with the width, not with the
  // lines or the bands.

  return [

    'size'    => [ 60, 12, 1000 ],
    'colors'  => [ '#f8f6f0', '#8fa9b8', '#c3a57a' ],
    'options' => [ 'lines' => [ 12, 2, 48 ] ],

    // A line of a band is a <use> of the wave the browser draws whole, so the budget is the
    // lines of all the bands.

    'count'   => [ 'lines', 2000, fn ( $p ) => ( (int) ceil ( $p ['h'] / $p ['size'] + 1.1 ) ) * (int) round ( $p ['lines'] ),
                   'a larger size, fewer lines or a smaller height' ],

    'draw'    => function ( $p ) {

      $size   = $p ['size'];
      $lines  = (int) round ( $p ['lines'] );
      $long   = $size * ( 1.4 + 1.2 * padPatternRand () );
      $fast   = 2 + padFakeNext () % 2;
      $weight = 0.25 + 0.35 * padPatternRand ();
      $phase  = 2 * M_PI * padPatternRand ();
      $high   = 0.6 * $size;
      $a      = $high / ( 1 + $weight );
      $b      = $high * $weight / ( 1 + $weight );
      $step   = min ( 3, $long / ( 12 * $fast ) );
      $id     = $p ['id'];
      $points = [];

      for ( $x = - $long; $x < $p ['w'] + $step; $x += $step ) {
        $t        = 2 * M_PI * $x / $long;
        $points[] = padPatternNum ( $x, 1 ) . ',' . padPatternNum ( $a * padPatternSin ( $t ) + $b * padPatternSin ( $fast * $t + $phase ), 1 );
      }

      // The lines of a band about y = 0: the k-th copy a k-th of a wavelength along, every
      // second one upside down.

      $band = '';

      for ( $k = 0; $k < $lines; $k++ ) {
        $x     = padPatternNum ( $long * $k / $lines, 1 );
        $turn  = ( $k % 2 ) ? " transform=\"matrix(1 0 0 -1 $x 0)\"" : " x=\"$x\"";
        $band .= "<use href=\"#$id-w\"$turn stroke=\"" . padPatternAttr ( $p ['ink'] [ $k % count ( $p ['ink'] ) ] ) . '"/>';
      }

      // The bands, their middles a size apart from half a size above the top, as far as one
      // reaches into the picture - a wave goes 0.6 of the size either way of its middle, so
      // the bands next to each other interlace.

      $body = "<g id=\"$id-b\" transform=\"translate(0 -" . padPatternNum ( $size / 2 ) . ")\">$band</g>";

      for ( $j = 1; ( $j - 1.1 ) * $size < $p ['h']; $j++ )
        $body .= "<use href=\"#$id-b\" y=\"" . padPatternNum ( $j * $size ) . '"/>';

      return [ "<path id=\"$id-w\" d=\"M" . implode ( ' ', $points ) . '"/>',
               '<g fill="none" stroke-width="' . padPatternNum ( max ( 0.5, $size / 90 ) ) . "\">$body</g>" ];

    }

  ];

?>
