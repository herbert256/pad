<?php

  // The edges filter by what Sobel's operator gives, worked out by hand, on pictures made here
  // with GD - each row of the result as its greys, a transparent pixel marked /t.
  //
  // One grey everywhere: nothing changes, no edge, the border neither (it reads its own edge
  // pixels again). A white pixel on black: its four sides 2 x 255 / 4 = 127.5, 128; its
  // corners the length of ( 255, 255 ) / 4 = 90.2, 90; itself 0. A step from black to white:
  // the full 4 x 255 / 4 on the last black and the first white pixel. A ramp of 32 a step: 64
  // inside, 32 and 31 at the ends, where the border is read twice. Red against green: their
  // luminances 76 and 150, 74 apart. A white square on a transparent ground: the inside 0, its
  // outline 255 from the opacity's own edge, the ground still transparent. amount=50: the
  // step at half, 128.

  include_once PAD . 'subs/images/filters/_lib/filter.php';

  $edges = include PAD . 'subs/images/filters/types/edges.php';

  $canvas = function ( $w, $h, $fill ) {
    $image = imagecreatetruecolor ( $w, $h );
    imagealphablending ( $image, FALSE );
    imagesavealpha ( $image, TRUE );
    imagefilledrectangle ( $image, 0, 0, $w - 1, $h - 1, $fill );
    return $image;
  };

  $rows = function ( $image, $amount = 100 ) use ( $edges ) {
    $image = $edges ['apply'] ( $image, [ 'colors' => [ '#000000', '#ffffff' ], 'amount' => $amount ], 1 );
    $lines = [];
    for ( $y = 0; $y < imagesy ( $image ); $y++ ) {
      $line = [];
      for ( $x = 0; $x < imagesx ( $image ); $x++ ) {
        $c      = imagecolorat ( $image, $x, $y );
        $line[] = ( $c & 255 ) . ( ( $c >> 24 ) & 127 ? '/t' : '' );
      }
      $lines [] = implode ( ' ', $line );
    }
    return implode ( ' | ', $lines );
  };

  $impulse = $canvas ( 5, 5, 0x000000 );
  imagesetpixel ( $impulse, 2, 2, 0xffffff );

  $step = $canvas ( 6, 2, 0x000000 );
  imagefilledrectangle ( $step, 3, 0, 5, 1, 0xffffff );

  $stepAgain = imagecrop ( $step, [ 'x' => 0, 'y' => 0, 'width' => 6, 'height' => 1 ] );

  $ramp = $canvas ( 9, 1, 0x000000 );
  for ( $x = 0; $x < 9; $x++ )
    imagesetpixel ( $ramp, $x, 0, 0x010101 * min ( 255, $x * 32 ) );

  $colours = $canvas ( 4, 1, 0xff0000 );
  imagefilledrectangle ( $colours, 2, 0, 3, 0, 0x00ff00 );

  $square = $canvas ( 6, 5, 0x7f000000 );
  imagefilledrectangle ( $square, 1, 1, 4, 3, 0xffffff );

  $checks = [
    [ 'check' => 'one grey',           'got' => $rows ( $canvas ( 5, 3, 0x808080 ) ) ],
    [ 'check' => 'a white pixel',      'got' => $rows ( $impulse ) ],
    [ 'check' => 'a step',             'got' => $rows ( $step ) ],
    [ 'check' => 'a ramp',             'got' => $rows ( $ramp ) ],
    [ 'check' => 'red against green',  'got' => $rows ( $colours ) ],
    [ 'check' => 'a square on nothing', 'got' => $rows ( $square ) ],
    [ 'check' => 'a step at amount=50', 'got' => $rows ( $stepAgain, 50 ) ],
  ];

?>
