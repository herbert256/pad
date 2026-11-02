<?php

  // BlurHash - Wolt's compact placeholder of a picture (https://blurha.sh): the picture as a few
  // cosine components, a DC term (its average colour) and components x components - 1 AC
  // terms, written in base 83 in 6 + 2 x (terms - 1) characters, '4,3' 28 of them. The
  // blurhash filter (types/blurhash.php) encodes the filtered 1x picture and decodes the hash
  // again into a small picture that stands behind the <img> while it loads.
  //
  // padBlurhashEncode  the hash of pixels - a list of rows, each a list of [ r, g, b ]
  // padBlurhashDecode  the pixels a hash stands for, at a width and height of choice
  // padBlurhashPixels  the pixels of a GD picture, the form encode reads
  //
  // This is the reference algorithm of github.com/woltapp/blurhash, its C encoder and its
  // TypeScript decoder, step for step: sRGB to linear light by the sRGB curve, each component
  // the average over the pixels of the colour times cos ( pi x i / width ) cos ( pi y j /
  // height ), the AC terms normalised by 2; the DC term back to sRGB, rounded (+ 0.5,
  // truncated), as one 24-bit number; the largest absolute AC value quantised to 0..82 as
  // floor ( max x 166 - 0.5 ), and each AC channel as floor ( signPow ( v / max, 0.5 ) x 9 +
  // 9.5 ) clamped to 0..18, three of them one number of base 19. Decoding is the same backward,
  // punch 1. The TypeScript encoder takes the largest AC value without its sign - a picture
  // whose largest component is negative gets another hash there; the C encoder, the original,
  // takes it with abs, as this one does. The numbers are PHP's doubles where C has floats: a
  // value that lands within a float's precision of a quantisation step could round to the
  // other side - the hash then differs by one step of one component.

  const padBlurhashDigits = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz#$%*+,-.:;=?@[]^_{|}~';

  function padBlurhashEncode ( $pixels, $cx, $cy ) {

    $h = count ( $pixels );
    $w = $h ? count ( $pixels [0] ) : 0;

    if ( ! $w or $cx < 1 or $cx > 9 or $cy < 1 or $cy > 9 )
      return '';

    // The pixels in linear light, once; the cosines of every column and every row, once.

    $linear = [];

    for ( $v = 0; $v < 256; $v++ )
      $linear [$v] = padBlurhashLinear ( $v );

    $cosX = [];
    $cosY = [];

    for ( $i = 0; $i < $cx; $i++ )
      for ( $x = 0; $x < $w; $x++ )
        $cosX [$i] [$x] = cos ( M_PI * $i * $x / $w );

    for ( $j = 0; $j < $cy; $j++ )
      for ( $y = 0; $y < $h; $y++ )
        $cosY [$j] [$y] = cos ( M_PI * $j * $y / $h );

    $factors = [];

    for ( $j = 0; $j < $cy; $j++ )
      for ( $i = 0; $i < $cx; $i++ ) {
        $r = $g = $b = 0.0;
        for ( $y = 0; $y < $h; $y++ ) {
          $row = $pixels [$y];
          $cj  = $cosY [$j] [$y];
          for ( $x = 0; $x < $w; $x++ ) {
            $basis = $cosX [$i] [$x] * $cj;
            $r += $basis * $linear [ $row [$x] [0] ];
            $g += $basis * $linear [ $row [$x] [1] ];
            $b += $basis * $linear [ $row [$x] [2] ];
          }
        }
        $scale     = ( ( $i == 0 and $j == 0 ) ? 1 : 2 ) / ( $w * $h );
        $factors[] = [ $r * $scale, $g * $scale, $b * $scale ];
      }

    $dc   = array_shift ( $factors );
    $hash = padBlurhashBase83 ( ( $cx - 1 ) + ( $cy - 1 ) * 9, 1 );

    if ( $factors ) {
      $largest = 0.0;
      foreach ( $factors as $factor )
        $largest = max ( $largest, abs ( $factor [0] ), abs ( $factor [1] ), abs ( $factor [2] ) );
      $quantised = (int) max ( 0, min ( 82, floor ( $largest * 166 - 0.5 ) ) );
      $maximum   = ( $quantised + 1 ) / 166;
      $hash     .= padBlurhashBase83 ( $quantised, 1 );
    } else {
      $maximum   = 1;
      $hash     .= padBlurhashBase83 ( 0, 1 );
    }

    $hash .= padBlurhashBase83 ( ( padBlurhashSrgb ( $dc [0] ) << 16 ) + ( padBlurhashSrgb ( $dc [1] ) << 8 ) + padBlurhashSrgb ( $dc [2] ), 4 );

    foreach ( $factors as $factor ) {
      $quant = [];
      foreach ( $factor as $value )
        $quant [] = (int) max ( 0, min ( 18, floor ( padBlurhashSignPow ( $value / $maximum, 0.5 ) * 9 + 9.5 ) ) );
      $hash .= padBlurhashBase83 ( $quant [0] * 19 * 19 + $quant [1] * 19 + $quant [2], 2 );
    }

    return $hash;

  }

  // The pixels of a hash: rows of [ r, g, b ], or [] for a text that is no hash - one whose
  // length does not say its components, or with a character that is no digit of base 83.

  function padBlurhashDecode ( $hash, $w, $h, $punch = 1 ) {

    $hash = (string) $hash;

    if ( strlen ( $hash ) < 6 or strspn ( $hash, padBlurhashDigits ) != strlen ( $hash ) )
      return [];

    $flag = padBlurhashNumber ( $hash [0] );
    $cy   = intdiv ( $flag, 9 ) + 1;
    $cx   = $flag % 9 + 1;

    if ( strlen ( $hash ) != 4 + 2 * $cx * $cy )
      return [];

    $maximum = ( padBlurhashNumber ( $hash [1] ) + 1 ) / 166 * $punch;
    $colors  = [];

    for ( $k = 0; $k < $cx * $cy; $k++ ) {
      if ( $k == 0 ) {
        $value      = padBlurhashNumber ( substr ( $hash, 2, 4 ) );
        $colors [0] = [ padBlurhashLinear ( $value >> 16 ), padBlurhashLinear ( ( $value >> 8 ) & 255 ), padBlurhashLinear ( $value & 255 ) ];
      } else {
        $value       = padBlurhashNumber ( substr ( $hash, 4 + $k * 2, 2 ) );
        $colors [$k] = [ padBlurhashSignPow ( ( intdiv ( $value, 361 ) - 9 ) / 9, 2.0 ) * $maximum,
                         padBlurhashSignPow ( ( intdiv ( $value, 19 ) % 19 - 9 ) / 9, 2.0 ) * $maximum,
                         padBlurhashSignPow ( ( $value % 19 - 9 ) / 9, 2.0 ) * $maximum ];
      }
    }

    $pixels = [];

    for ( $y = 0; $y < $h; $y++ )
      for ( $x = 0; $x < $w; $x++ ) {
        $r = $g = $b = 0.0;
        for ( $j = 0; $j < $cy; $j++ )
          for ( $i = 0; $i < $cx; $i++ ) {
            $basis = cos ( M_PI * $x * $i / $w ) * cos ( M_PI * $y * $j / $h );
            $color = $colors [ $i + $j * $cx ];
            $r += $color [0] * $basis;
            $g += $color [1] * $basis;
            $b += $color [2] * $basis;
          }
        $pixels [$y] [$x] = [ padBlurhashSrgb ( $r ), padBlurhashSrgb ( $g ), padBlurhashSrgb ( $b ) ];
      }

    return $pixels;

  }

  function padBlurhashPixels ( $image ) {

    $w      = imagesx ( $image );
    $h      = imagesy ( $image );
    $pixels = [];

    for ( $y = 0; $y < $h; $y++ )
      for ( $x = 0; $x < $w; $x++ ) {
        $c = imagecolorat ( $image, $x, $y );
        $pixels [$y] [$x] = [ ( $c >> 16 ) & 255, ( $c >> 8 ) & 255, $c & 255 ];
      }

    return $pixels;

  }

  function padBlurhashLinear ( $value ) {

    $v = $value / 255;

    return ( $v <= 0.04045 ) ? $v / 12.92 : ( ( $v + 0.055 ) / 1.055 ) ** 2.4;

  }

  function padBlurhashSrgb ( $value ) {

    $v = max ( 0, min ( 1, $value ) );

    return ( $v <= 0.0031308 ) ? (int) ( $v * 12.92 * 255 + 0.5 ) : (int) ( ( 1.055 * $v ** ( 1 / 2.4 ) - 0.055 ) * 255 + 0.5 );

  }

  function padBlurhashSignPow ( $value, $exp ) {

    return ( $value < 0 ? -1 : 1 ) * abs ( $value ) ** $exp;

  }

  function padBlurhashBase83 ( $value, $length ) {

    $text = '';

    for ( $k = 1; $k <= $length; $k++ )
      $text .= padBlurhashDigits [ intdiv ( $value, 83 ** ( $length - $k ) ) % 83 ];

    return $text;

  }

  function padBlurhashNumber ( $text ) {

    $value = 0;

    foreach ( str_split ( $text ) as $char )
      $value = $value * 83 + strpos ( padBlurhashDigits, $char );

    return $value;

  }

?>
