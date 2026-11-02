<?php

  // The threshold filter on a ramp of every grey, 0 to 255, worked out by hand: the cut is
  // threshold x 255 / 100, and the first grey of paper is the first at or past it - 128 at 50
  // (127.5), 64 at 25 (63.75), 0 at 0 (everything paper), 255 at 100 (only white). A colour
  // is cut by its luminance: red 76 and green 150 at 50 are ink and paper. A pixel keeps its
  // opacity: a transparent one stays transparent, one of half keeps its half.

  include_once PAD . 'subs/filters/_lib/filter.php';

  $spec = include PAD . 'subs/filters/types/threshold.php';

  $ramp = function () {
    $image = imagecreatetruecolor ( 256, 1 );
    for ( $x = 0; $x < 256; $x++ )
      imagesetpixel ( $image, $x, 0, 0x010101 * $x );
    return $image;
  };

  $firstPaper = function ( $threshold ) use ( $spec, $ramp ) {
    $image = $spec ['apply'] ( $ramp (), [ 'threshold' => $threshold, 'colors' => [ '#000000', '#ffffff' ] ], 1 );
    for ( $x = 0; $x < 256; $x++ )
      if ( imagecolorat ( $image, $x, 0 ) == 0xffffff )
        return "grey $x, " . ( 256 - $x ) . ' of 256 paper';
    return 'none';
  };

  $pixels = function ( $image, $o ) use ( $spec ) {
    $image = $spec ['apply'] ( $image, $o, 1 );
    $out   = [];
    for ( $x = 0; $x < imagesx ( $image ); $x++ ) {
      $c     = imagecolorat ( $image, $x, 0 );
      $out[] = sprintf ( '#%06x', $c & 0xFFFFFF ) . ( ( $c >> 24 ) & 127 ? ' alpha ' . ( ( $c >> 24 ) & 127 ) : '' );
    }
    return implode ( ' ', $out );
  };

  $colours = imagecreatetruecolor ( 2, 1 );
  imagesetpixel ( $colours, 0, 0, 0xff0000 );
  imagesetpixel ( $colours, 1, 0, 0x00ff00 );

  $clear = imagecreatetruecolor ( 2, 1 );
  imagealphablending ( $clear, FALSE );
  imagesetpixel ( $clear, 0, 0, 0x7fffffff );
  imagesetpixel ( $clear, 1, 0, 0x40ffffff );

  $checks = [
    [ 'check' => 'at 50',                'got' => $firstPaper ( 50 ) ],
    [ 'check' => 'at 25',                'got' => $firstPaper ( 25 ) ],
    [ 'check' => 'at 0',                 'got' => $firstPaper ( 0 ) ],
    [ 'check' => 'at 100',               'got' => $firstPaper ( 100 ) ],
    [ 'check' => 'red and green',        'got' => $pixels ( $colours, [ 'threshold' => 50, 'colors' => [ '#123', '#fc6' ] ] ) ],
    [ 'check' => 'transparent and half', 'got' => $pixels ( $clear, [ 'threshold' => 50, 'colors' => [ '#000000', '#ffffff' ] ] ) ],
  ];

?>
