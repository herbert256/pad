<?php

  // BlurHash by what the reference algorithm gives, on pictures made here with GD.
  //
  // A solid #3366cc of 8 by 8 at 4,3: the size flag L (3 + 2 x 9 = 21), the DC term 5?~E -
  // 0x3366cc in four digits of base 83 - and AC terms that are not all zero: the reference
  // takes cos ( pi i x / width ) for x = 0 .. width - 1, whose sum over a row is 1 for an odd i,
  // so a solid picture of 8 columns has AC ( 1, 0 ) of a quarter of its linear colour - blue
  // 0.604 / 4 = 0.151, the largest, quantised to floor ( 0.151 x 166 - 0.5 ) = 24, O - and
  // p3 for the terms of odd i or j alone (red 11, green 13, blue 18 of base 19), j^ for those
  // of both (10, 10, 12), fQ (9, 9, 9: zero) for the even ones. Worked out by hand, this is the
  // hash below, character for character. One component is the DC term alone: 00 5?~E.
  //
  // Black to white across, 2,1: the one AC term is negative for every channel (00: 0, 0, 0,
  // the most negative step), and the decoded row goes from dark to light.
  //
  // The photo: its hash decoded, encoded again and decoded again is the same picture within a
  // few levels - the reference's decode and encode are not each other's exact inverse (the
  // largest AC term comes back quantised, the pixels clamped and rounded), so the second hash
  // is not the first, but what it draws is. And the photo against its hash's picture: a blur
  // of it. A text of a length no components give decodes to nothing.

  include_once PAD . 'subs/filters/_lib/blurhash.php';

  $solid = imagecreatetruecolor ( 8, 8 );
  imagefill ( $solid, 0, 0, 0x3366cc );

  $ramp = imagecreatetruecolor ( 16, 4 );

  for ( $x = 0; $x < 16; $x++ )
    imagefilledrectangle ( $ramp, $x, 0, $x, 3, 0x111111 * $x );

  $rampHash = padBlurhashEncode ( padBlurhashPixels ( $ramp ), 2, 1 );

  $photo = imagecreatefromjpeg ( dirname ( APPS ) . '/www/regression/framework/assets/photo.jpg' );
  $small = imagecreatetruecolor ( 32, 21 );
  imagecopyresampled ( $small, $photo, 0, 0, 0, 0, 32, 21, 320, 213 );

  $first   = padBlurhashEncode ( padBlurhashPixels ( $small ), 4, 3 );
  $drawn   = padBlurhashDecode ( $first, 32, 21 );
  $second  = padBlurhashEncode ( padBlurhashDecode ( $first, 320, 213 ), 4, 3 );
  $again   = padBlurhashDecode ( $second, 32, 21 );
  $apart   = function ( $one, $two ) {
    $sum = 0;
    foreach ( $one as $y => $row )
      foreach ( $row as $x => $pixel )
        foreach ( [ 0, 1, 2 ] as $c )
          $sum += abs ( $pixel [$c] - $two [$y] [$x] [$c] );
    return number_format ( $sum / ( count ( $one ) * count ( $one [0] ) * 3 ), 1 );
  };

  $checks = [
    [ 'check' => 'solid #3366cc',                'got' => padBlurhashEncode ( padBlurhashPixels ( $solid ), 4, 3 ) ],
    [ 'check' => 'one component',                'got' => padBlurhashEncode ( padBlurhashPixels ( $solid ), 1, 1 ) ],
    [ 'check' => 'black to white',               'got' => $rampHash ],
    [ 'check' => 'decoded across',               'got' => implode ( ' ', array_map ( fn ( $p ) => $p [0], padBlurhashDecode ( $rampHash, 4, 1 ) [0] ) ) ],
    [ 'check' => 'the photo',                    'got' => $first ],
    [ 'check' => 'decoded and encoded again',    'got' => $second ],
    [ 'check' => 'the two pictures apart',       'got' => $apart ( $drawn, $again ) . ' levels' ],
    [ 'check' => 'the photo and its picture',    'got' => $apart ( padBlurhashPixels ( $small ), $drawn ) . ' levels' ],
    [ 'check' => 'a hash of the wrong length',   'got' => count ( padBlurhashDecode ( 'L05?~E', 4, 3 ) ) . ' pixels' ],
  ];

?>
