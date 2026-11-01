<?php

  // dominant - a placeholder: the picture's dominant colour behind the <img> while it loads, and
  // in data-color for a page's own use.
  //
  //   {img 'photos/harbour.jpg', width=400, filter='dominant', alt='The harbour'/}
  //
  // Every pixel of the 1x picture, the filters before it applied, is counted in a box of 16
  // steps of red, green and blue; the colour is the average of the pixels of the fullest box
  // - of two equally full, the one of less red, then less green, then less blue. A pixel
  // counts by its opacity: a transparent one not at all, where it counted as black and a logo
  // on a transparent ground got a black placeholder.

  return [

    'form'  => 'placeholder',
    'loops' => TRUE,

    'hold'  => function ( $image, $o ) {

      $w     = imagesx ( $image );
      $h     = imagesy ( $image );
      $boxes = [];

      for ( $y = 0; $y < $h; $y++ )
        for ( $x = 0; $x < $w; $x++ ) {
          $c      = imagecolorat ( $image, $x, $y );
          $weight = 127 - ( ( $c >> 24 ) & 127 );
          if ( ! $weight )
            continue;
          $box = ( ( $c >> 20 ) & 15 ) << 8 | ( ( $c >> 12 ) & 15 ) << 4 | ( ( $c >> 4 ) & 15 );
          if ( ! isset ( $boxes [$box] ) )
            $boxes [$box] = [ 0, 0, 0, 0 ];
          $boxes [$box] [0] += $weight;
          $boxes [$box] [1] += $weight * ( ( $c >> 16 ) & 255 );
          $boxes [$box] [2] += $weight * ( ( $c >> 8 ) & 255 );
          $boxes [$box] [3] += $weight * ( $c & 255 );
        }

      // A picture transparent everywhere has no colour to show: the quiet colour of every
      // picture stays behind it.

      if ( ! $boxes )
        return [];

      ksort ( $boxes );

      $best = NULL;

      foreach ( $boxes as $box )
        if ( $best === NULL or $box [0] > $best [0] )
          $best = $box;

      $color = sprintf ( '#%02x%02x%02x', (int) round ( $best [1] / $best [0] ), (int) round ( $best [2] / $best [0] ), (int) round ( $best [3] / $best [0] ) );

      return [ 'style' => [ 'background-color' => $color ], 'attrs' => [ 'data-color' => $color ] ];

    }

  ];

?>
