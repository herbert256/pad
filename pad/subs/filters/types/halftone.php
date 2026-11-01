<?php

  // halftone - the picture as dots of ink on paper, an SVG in place of the <img>: a dot every
  // cell= pixels (8) on rows set off by half a dot, its area the darkness there; the first of
  // colors= the ink, the second the paper. The alt text names the SVG; alt='' leaves it
  // decoration.
  //
  //   {img 'photos/harbour.jpg', width=400, filter='halftone', cell=6, alt='The harbour'/}
  //
  // The rows are a cell times the height of an equilateral triangle apart, so every dot is a
  // cell from its six neighbours; the picture is made as small as the cells - each pixel the
  // average of a cell - and read where a dot stands. A dot's radius is half a cell times the
  // square root of the darkness, a little more so the darkest dots touch.

  return [

    'form'    => 'markup',
    'ext'     => 'svg',
    'options' => [ 'cell' => [ 'number', 8, 3, 100 ], 'colors' => [ 'colors', [ '#1a1a19', '#fcfcfb' ] ] ],

    'make'    => function ( $image, $o ) {

      $w     = imagesx ( $image );
      $h     = imagesy ( $image );
      $cell  = $o ['cell'];
      $rise  = $cell * sqrt ( 3 ) / 2;
      $sw    = max ( 1, (int) ceil ( $w / $cell ) );
      $sh    = max ( 1, (int) ceil ( $h / $cell ) );
      $lumas = padFilterLumas ( padFilterResize ( $image, $sw, $sh ) );
      $dots  = '';

      for ( $j = 0; $j * $rise < $h + $rise; $j++ )
        for ( $i = 0; $i * $cell < $w + $cell; $i++ ) {
          $x    = $i * $cell + ( $j % 2 ? $cell / 2 : 0 );
          $y    = $j * $rise;
          $luma = $lumas [ min ( $sh - 1, (int) floor ( $y / $cell ) ) ] [ min ( $sw - 1, (int) floor ( $x / $cell ) ) ];
          $r    = $cell / 2 * 1.1 * sqrt ( 1 - $luma / 255 );
          if ( $r >= 0.2 and $x - $r < $w and $y - $r < $h )
            $dots .= '<circle cx="' . padFilterNum ( $x ) . '" cy="' . padFilterNum ( $y ) . '" r="' . padFilterNum ( $r ) . '"/>';
        }

      return "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"$w\" height=\"$h\" viewBox=\"0 0 $w $h\">"
           . "<rect width=\"$w\" height=\"$h\" fill=\"{$o ['colors'] [1]}\"/><g fill=\"{$o ['colors'] [0]}\">$dots</g></svg>";

    },

    'show'    => function ( $kept, $o, $w, $h, $alt ) {

      $open  = strpos ( $kept, '>' );
      $close = strrpos ( $kept, '</svg>' );
      $name  = padImgAttr ( $alt );

      return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"pad-img-halftone\" width=\"$w\" height=\"$h\" viewBox=\"0 0 $w $h\""
           . ( $alt !== '' ? " role=\"img\" aria-label=\"$name\"><title>$name</title>" : ' aria-hidden="true" focusable="false">' )
           . substr ( $kept, $open + 1, $close - $open - 1 ) . '</svg>';

    }

  ];

?>
