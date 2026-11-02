<?php

  // autocontrast - the picture's tones stretched over the whole range: its darkest pixels made
  // black and its lightest white, cutoff= percent (1) of them at each end let go first, so a
  // grey, misty or faded picture gets its depth back - the colours keep their relations.
  //
  //   {pad:img 'photos/harbour.jpg', width=400, filter='autocontrast', cutoff=2, alt='The harbour'/}
  //
  // PIL's ImageOps.autocontrast with preserve_tone, step for step: the histogram of the
  // luminance (Rec. 601, 0 to 255), cutoff percent of the pixels taken off its dark end and as
  // many off its light end, lo and hi the darkest and lightest left, and one table for red,
  // green and blue alike - a value v becomes ( v - lo ) x 255 / ( hi - lo ), cut to a whole
  // number and kept from 0 to 255 - so a colour is not shifted, where a table for each channel
  // also pulls out a cast. A picture of one tone, hi not above lo, is left as it is. PIL's
  // cutoff is 0 unless said; here it is 1, as a camera's picture has a black and a white
  // pixel somewhere, which left it as it was.
  //
  // Transparency: the histogram counts the pixels that show - a transparent one not at all,
  // as PIL with the alpha as its mask - so the hidden colours of a logo's ground do not decide
  // its stretch; every pixel keeps its opacity.

  return [

    'form'    => 'image',
    'loops'   => TRUE,
    'options' => [ 'cutoff' => [ 'number', 1, 0, 49 ] ],

    'apply'   => function ( $image, $o, $scale ) {

      $w     = imagesx ( $image );
      $h     = imagesy ( $image );
      $histo = array_fill ( 0, 256, 0 );

      for ( $y = 0; $y < $h; $y++ )
        for ( $x = 0; $x < $w; $x++ ) {
          $c = imagecolorat ( $image, $x, $y );
          if ( ( ( $c >> 24 ) & 127 ) < 127 )
            $histo [ padFilterLuma ( ( $c >> 16 ) & 255, ( $c >> 8 ) & 255, $c & 255 ) ]++;
        }

      // cutoff percent of the pixels off each end: what is left of a bin is what was not cut.

      $n = array_sum ( $histo );

      foreach ( [ range ( 0, 255 ), range ( 255, 0 ) ] as $order ) {
        $cut = (int) floor ( $n * $o ['cutoff'] / 100 );
        foreach ( $order as $v ) {
          if ( $cut <= 0 )
            break;
          $take        = min ( $cut, $histo [$v] );
          $histo [$v] -= $take;
          $cut        -= $take;
        }
      }

      for ( $lo = 0; $lo < 255 and ! $histo [$lo]; $lo++ ) ;
      for ( $hi = 255; $hi > 0 and ! $histo [$hi]; $hi-- ) ;

      if ( $hi <= $lo )
        return $image;

      $stretch = 255.0 / ( $hi - $lo );
      $offset  = - $lo * $stretch;
      $table   = [];

      for ( $v = 0; $v < 256; $v++ )
        $table [$v] = max ( 0, min ( 255, (int) ( $v * $stretch + $offset ) ) );

      return padFilterMap ( $image, fn ( $r, $g, $b ) => ( $table [$r] << 16 ) | ( $table [$g] << 8 ) | $table [$b] );

    }

  ];

?>
