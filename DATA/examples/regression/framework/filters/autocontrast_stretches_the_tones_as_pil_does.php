<?php

  // The autocontrast filter by PIL's ImageOps.autocontrast with preserve_tone, worked out by
  // hand on pictures made here with GD.
  //
  // A ramp of the greys 64 to 191, one pixel each, at cutoff 0: lo 64 and hi 191, a value v
  // becomes ( v - 64 ) x 255 / 127 cut to a whole number - 64 0, 100 72, 128 128, 191 255. At
  // cutoff 10: 12 of its 128 pixels let go at each end, lo 76 and hi 179 - 76 0, 128 128, 179
  // 254 (254.99999 in doubles, cut, as PIL's own table has it), and what lies beyond them kept
  // at 0 and 255. A colour goes through the same table on every channel: #806040 among those
  // greys is #804000 - each channel stretched as a grey of its value is, not each to a range
  // of its own. A picture of one grey: as it was. A ramp of 100 to 150 with a transparent
  // black and a transparent white pixel beside it: the stretch is of the 100 to 150 alone,
  // 125 becoming 127, the two keeping their opacity. Every value here is what PIL 13's
  // ImageOps.autocontrast ( preserve_tone=True ) gives for the same pixels, the photo's
  // thumbnails too.

  include_once PAD . 'subs/filters/_lib/filter.php';

  $spec = include PAD . 'subs/filters/types/autocontrast.php';

  $ramp = function ( $from, $to, $extra = [] ) {
    $image = imagecreatetruecolor ( $to - $from + 1 + count ( $extra ), 1 );
    imagealphablending ( $image, FALSE );
    for ( $v = $from; $v <= $to; $v++ )
      imagesetpixel ( $image, $v - $from, 0, 0x010101 * $v );
    foreach ( $extra as $k => $c )
      imagesetpixel ( $image, $to - $from + 1 + $k, 0, $c );
    return $image;
  };

  $at = function ( $image, $cutoff, $xs ) use ( $spec ) {
    $image = $spec ['apply'] ( $image, [ 'cutoff' => $cutoff ], 1 );
    $out   = [];
    foreach ( $xs as $label => $x ) {
      $c     = imagecolorat ( $image, $x, 0 );
      $out[] = "$label " . ( ( ( $c >> 16 ) & 255 ) == ( $c & 255 ) && ( ( $c >> 8 ) & 255 ) == ( $c & 255 ) ? $c & 255 : sprintf ( '#%06x', $c & 0xFFFFFF ) )
             . ( ( $c >> 24 ) & 127 ? ' alpha ' . ( ( $c >> 24 ) & 127 ) : '' );
    }
    return implode ( ', ', $out );
  };

  $flat = imagecreatetruecolor ( 4, 2 );
  imagefill ( $flat, 0, 0, 0x6a6a6a );

  $checks = [
    [ 'check' => 'greys 64 to 191 at 0',    'got' => $at ( $ramp ( 64, 191 ), 0, [ 64 => 0, 100 => 36, 128 => 64, 191 => 127 ] ) ],
    [ 'check' => 'at 10',                   'got' => $at ( $ramp ( 64, 191 ), 10, [ 64 => 0, 76 => 12, 128 => 64, 179 => 115, 191 => 127 ] ) ],
    [ 'check' => 'a colour among them',     'got' => $at ( $ramp ( 64, 191, [ 0x806040 ] ), 0, [ '#806040' => 128 ] ) ],
    [ 'check' => 'one grey',                'got' => $at ( $flat, 1, [ '#6a6a6a' => 0 ] ) ],
    [ 'check' => 'beside transparent ones', 'got' => $at ( $ramp ( 100, 150, [ 0x7f000000, 0x7fffffff ] ), 0, [ 100 => 0, 125 => 25, 150 => 50, 'black' => 51, 'white' => 52 ] ) ],
  ];

?>
