<?php

  // The edges filter on the 2x picture reads its neighbours a CSS pixel away - $scale pixels -
  // so a screen of any density shows the lines of the 1x picture: as strong, and as wide in
  // CSS pixels. Worked out by hand, white lines on black at amount=100 (a quarter of the
  // length of the gradient):
  //
  // A step from black to white: at 1x the full 255 on the pixel either side of it; at 2x on
  // the two either side - two CSS pixels in both, where the 2x picture had one CSS pixel.
  // A ramp of 32 a CSS pixel: 32 at 1x is 64 inside (4 x 2 x 32 / 4); 16 a pixel at 2x read
  // two pixels away is 64 too, where it was 32. At 1.5x, 20 a pixel: the gradients of the
  // strides 1 and 2 weighed half and half, 40 and 80, is 60 - 30 a CSS pixel, as 1x draws it.
  // The framework's photo, 160 wide at 1x and 320 at 2x: the mean of the lines about the
  // same, where the 2x picture's was about half.

  include_once PAD . 'subs/images/filters/_lib/filter.php';

  $edges = include PAD . 'subs/images/filters/types/edges.php';

  $row = function ( $values, $scale ) use ( $edges ) {
    $image = imagecreatetruecolor ( count ( $values ), 1 );
    foreach ( $values as $x => $v )
      imagesetpixel ( $image, $x, 0, 0x010101 * $v );
    $image = $edges ['apply'] ( $image, [ 'colors' => [ '#000000', '#ffffff' ], 'amount' => 100 ], $scale );
    $out = [];
    for ( $x = 0; $x < imagesx ( $image ); $x++ )
      $out [] = imagecolorat ( $image, $x, 0 ) & 255;
    return implode ( ' ', $out );
  };

  $mean = function ( $width, $scale ) use ( $edges ) {
    $photo = imagecreatefromjpeg ( dirname ( APPS ) . '/www/regression/framework/assets/photo.jpg' );
    $image = $edges ['apply'] ( padFilterResize ( $photo, $width, (int) round ( $width * 213 / 320 ) ), [ 'colors' => [ '#000000', '#ffffff' ], 'amount' => 100 ], $scale );
    $sum   = 0;
    for ( $y = 0; $y < imagesy ( $image ); $y++ )
      for ( $x = 0; $x < imagesx ( $image ); $x++ )
        $sum += imagecolorat ( $image, $x, $y ) & 255;
    return number_format ( $sum / ( imagesx ( $image ) * imagesy ( $image ) ), 1 );
  };

  $checks = [
    [ 'check' => 'a step at 1x',          'got' => $row ( [ 0, 0, 0, 255, 255, 255 ], 1 ) ],
    [ 'check' => 'a step at 2x',          'got' => $row ( [ 0, 0, 0, 0, 0, 0, 255, 255, 255, 255, 255, 255 ], 2 ) ],
    [ 'check' => 'a ramp of 32 at 1x',    'got' => $row ( range ( 0, 224, 32 ), 1 ) ],
    [ 'check' => 'a ramp of 16 at 2x',    'got' => $row ( range ( 0, 224, 16 ), 2 ) ],
    [ 'check' => 'a ramp of 20 at 1.5x',  'got' => $row ( range ( 0, 220, 20 ), 1.5 ) ],
    [ 'check' => 'the photo at 1x and 2x', 'got' => $mean ( 160, 1 ) . ' and ' . $mean ( 320, 2 ) ],
  ];

?>
