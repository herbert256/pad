<?php

  // ThumbHash by what the reference implementation gives - js/thumbhash.js of
  // github.com/evanw/thumbhash, rgbaToThumbHash and thumbHashToRGBA run by node on the same
  // RGBA pixels: every hash below is the reference's, byte for byte, and its picture decoded
  // the same bytes as the reference decodes.
  //
  // The photo and the picture with transparent parts, made 100 by 67 as the filter makes them:
  // the photo's hash has no alpha flag (bit 7 of its third byte), the other has, and decodes 32
  // by 19 - its shape - with a transparent corner. A ramp from black to white down a strip of
  // 20 by 60 decodes standing, 9 by 32. One pixel of #ff8000. A picture transparent
  // everywhere. A solid #3366cc: its terms beyond the average are no more than rounding noise,
  // whose nibbles depend on the last bit of a cosine - JavaScript's engine and the C library
  // can part there - so the check is what it draws, one colour, where the noise counts for
  // nothing (its scale quantises to 0). A hash shorter than its header decodes to nothing.

  include_once PAD . 'subs/filters/_lib/thumbhash.php';

  $fit = function ( $source ) {
    $out = imagecreatetruecolor ( 100, 67 );
    imagealphablending ( $out, FALSE );
    imagesavealpha ( $out, TRUE );
    imagefill ( $out, 0, 0, imagecolorallocatealpha ( $out, 0, 0, 0, 127 ) );
    imagecopyresampled ( $out, $source, 0, 0, 0, 0, 100, 67, imagesx ( $source ), imagesy ( $source ) );
    return $out;
  };

  $hashOf = fn ( $image ) => padThumbhashEncode ( imagesx ( $image ), imagesy ( $image ), padThumbhashPixels ( $image ) );

  $drawn = function ( $hash ) {
    list ( $w, $h, $rgba ) = padThumbhashDecode ( $hash );
    if ( ! $w )
      return 'nothing';
    $corner = vsprintf ( '#%02x%02x%02x opacity %d', array_slice ( $rgba, 0, 4 ) );
    $colours = [];
    for ( $i = 0; $i < count ( $rgba ); $i += 4 )
      $colours [ implode ( ',', array_slice ( $rgba, $i, 4 ) ) ] = TRUE;
    return "{$w}x$h corner $corner colours " . count ( $colours ) . ' md5 ' . md5 ( implode ( ',', $rgba ) );
  };

  $www   = dirname ( APPS ) . '/www/regression/framework/assets';
  $photo = $hashOf ( $fit ( imagecreatefromjpeg ( "$www/photo.jpg" ) ) );
  $alpha = $hashOf ( $fit ( imagecreatefrompng ( "$www/alpha.png" ) ) );

  $ramp = imagecreatetruecolor ( 20, 60 );
  for ( $y = 0; $y < 60; $y++ )
    imagefilledrectangle ( $ramp, 0, $y, 19, $y, 0x010101 * (int) ( $y * 255 / 59 ) );

  $one = imagecreatetruecolor ( 1, 1 );
  imagefill ( $one, 0, 0, 0xff8000 );

  $clear = imagecreatetruecolor ( 6, 4 );
  imagealphablending ( $clear, FALSE );
  imagefill ( $clear, 0, 0, 0x7f000000 );

  $solid = imagecreatetruecolor ( 8, 8 );
  imagefill ( $solid, 0, 0, 0x3366cc );

  $checks = [
    [ 'check' => 'the photo',                    'got' => base64_encode ( $photo ) ],
    [ 'check' => 'its alpha flag',               'got' => ( ord ( $photo [2] ) & 0x80 ) ? 'set' : 'not set' ],
    [ 'check' => 'decoded',                      'got' => $drawn ( $photo ) ],
    [ 'check' => 'with transparent parts',       'got' => base64_encode ( $alpha ) ],
    [ 'check' => 'its alpha flag',               'got' => ( ord ( $alpha [2] ) & 0x80 ) ? 'set' : 'not set' ],
    [ 'check' => 'decoded',                      'got' => $drawn ( $alpha ) ],
    [ 'check' => 'a ramp standing',              'got' => base64_encode ( $hashOf ( $ramp ) ) ],
    [ 'check' => 'decoded',                      'got' => $drawn ( $hashOf ( $ramp ) ) ],
    [ 'check' => 'one pixel',                    'got' => base64_encode ( $hashOf ( $one ) ) ],
    [ 'check' => 'transparent everywhere',       'got' => base64_encode ( $hashOf ( $clear ) ) ],
    [ 'check' => 'decoded',                      'got' => $drawn ( $hashOf ( $clear ) ) ],
    [ 'check' => 'solid #3366cc decoded',        'got' => $drawn ( $hashOf ( $solid ) ) ],
    [ 'check' => 'a hash shorter than a header', 'got' => $drawn ( "\x1d\x94\x01" ) ],
  ];

?>
