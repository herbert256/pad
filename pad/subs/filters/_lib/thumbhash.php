<?php

  // ThumbHash - Evan Wallace's compact placeholder of a picture (https://evanw.github.io/thumbhash/):
  // the picture as a few cosine terms of its luminance, two colour differences and - when it
  // has transparent parts - its opacity, in some 25 bytes, which also hold its shape: a
  // placeholder of the right proportion, more detail than a BlurHash of the same size, and
  // transparency kept. The thumbhash filter (types/thumbhash.php) encodes the filtered 1x
  // picture and decodes the hash again into the small picture that stands behind the <img>
  // while it loads.
  //
  // padThumbhashEncode  the hash - a binary string - of RGBA pixels, at most 100 by 100
  // padThumbhashDecode  the picture a hash stands for: [ width, height, RGBA pixels ], at most
  //                     32 pixels on its longer side, the shape the hash holds
  // padThumbhashPixels  the RGBA pixels of a GD picture, the form encode reads
  //
  // A port of the reference implementation, js/thumbhash.js of github.com/evanw/thumbhash,
  // Copyright (c) 2023 Evan Wallace, MIT licence - rgbaToThumbHash and thumbHashToRGBA step for
  // step, every sum in the same order, so the doubles come out bit for bit as JavaScript's:
  // the colour laid on its own average where it is transparent, RGB to L (the mean), P (yellow
  // against blue) and Q (red against green), each channel's DCT terms over the pixel centres
  // (cos ( pi / w x cx x ( x + 0.5 ) )), the terms normalised by the largest and written as
  // 4-bit nibbles after a 5-byte header (6 with transparency); Math.round is rounding a half up,
  // done here with floor, as PHP's round rounds a half away from zero; a decoded channel is
  // stored as a Uint8Array stores it, cut, not rounded. The hashes of the framework cases are
  // what the reference's own code gives for the same pixels.
  //
  // The MIT licence of the reference: Permission is hereby granted, free of charge, to any
  // person obtaining a copy of this software and associated documentation files (the
  // "Software"), to deal in the Software without restriction, including without limitation
  // the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  // copies of the Software, and to permit persons to whom the Software is furnished to do so,
  // subject to the following conditions: The above copyright notice and this permission
  // notice shall be included in all copies or substantial portions of the Software. THE
  // SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
  // BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
  // NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
  // CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
  // ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
  // SOFTWARE.

  // JavaScript's Math.round of the values here, none below zero: the half goes up.

  function padThumbhashRound ( $x ) {

    $floor = floor ( $x );

    return (int) ( ( $x - $floor >= 0.5 ) ? $floor + 1 : $floor );

  }

  function padThumbhashEncode ( $w, $h, $rgba ) {

    if ( $w < 1 or $h < 1 or $w > 100 or $h > 100 or count ( $rgba ) != $w * $h * 4 )
      return '';

    // The average colour, each pixel weighed by its opacity.

    $avgR = $avgG = $avgB = $avgA = 0;

    for ( $i = 0, $j = 0; $i < $w * $h; $i++, $j += 4 ) {
      $alpha = $rgba [$j + 3] / 255;
      $avgR += $alpha / 255 * $rgba [$j];
      $avgG += $alpha / 255 * $rgba [$j + 1];
      $avgB += $alpha / 255 * $rgba [$j + 2];
      $avgA += $alpha;
    }

    if ( $avgA ) {
      $avgR /= $avgA;
      $avgG /= $avgA;
      $avgB /= $avgA;
    }

    $hasAlpha = ( $avgA < $w * $h );
    $limit    = $hasAlpha ? 5 : 7;
    $lx       = max ( 1, padThumbhashRound ( $limit * $w / max ( $w, $h ) ) );
    $ly       = max ( 1, padThumbhashRound ( $limit * $h / max ( $w, $h ) ) );
    $l        = [];
    $p        = [];
    $q        = [];
    $a        = [];

    // RGBA to LPQA, a transparent part laid on the average colour.

    for ( $i = 0, $j = 0; $i < $w * $h; $i++, $j += 4 ) {
      $alpha  = $rgba [$j + 3] / 255;
      $r      = $avgR * ( 1 - $alpha ) + $alpha / 255 * $rgba [$j];
      $g      = $avgG * ( 1 - $alpha ) + $alpha / 255 * $rgba [$j + 1];
      $b      = $avgB * ( 1 - $alpha ) + $alpha / 255 * $rgba [$j + 2];
      $l [$i] = ( $r + $g + $b ) / 3;
      $p [$i] = ( $r + $g ) / 2 - $b;
      $q [$i] = $r - $g;
      $a [$i] = $alpha;
    }

    $channel = function ( $values, $nx, $ny ) use ( $w, $h ) {

      $dc    = 0;
      $ac    = [];
      $scale = 0;

      for ( $cy = 0; $cy < $ny; $cy++ )
        for ( $cx = 0; $cx * $ny < $nx * ( $ny - $cy ); $cx++ ) {
          $f  = 0;
          $fx = [];
          for ( $x = 0; $x < $w; $x++ )
            $fx [$x] = cos ( M_PI / $w * $cx * ( $x + 0.5 ) );
          for ( $y = 0; $y < $h; $y++ ) {
            $fy = cos ( M_PI / $h * $cy * ( $y + 0.5 ) );
            for ( $x = 0; $x < $w; $x++ )
              $f += $values [ $x + $y * $w ] * $fx [$x] * $fy;
          }
          $f /= $w * $h;
          if ( $cx or $cy ) {
            $ac []  = $f;
            $scale = max ( $scale, abs ( $f ) );
          } else
            $dc = $f;
        }

      if ( $scale )
        foreach ( $ac as $k => $f )
          $ac [$k] = 0.5 + 0.5 / $scale * $f;

      return [ $dc, $ac, $scale ];

    };

    list ( $lDc, $lAc, $lScale ) = $channel ( $l, max ( 3, $lx ), max ( 3, $ly ) );
    list ( $pDc, $pAc, $pScale ) = $channel ( $p, 3, 3 );
    list ( $qDc, $qAc, $qScale ) = $channel ( $q, 3, 3 );

    $aDc = $aAc = $aScale = NULL;

    if ( $hasAlpha )
      list ( $aDc, $aAc, $aScale ) = $channel ( $a, 5, 5 );

    // The header, then the terms two to a byte, the first in the low nibble.

    $landscape = ( $w > $h );
    $header24  = padThumbhashRound ( 63 * $lDc ) | ( padThumbhashRound ( 31.5 + 31.5 * $pDc ) << 6 )
               | ( padThumbhashRound ( 31.5 + 31.5 * $qDc ) << 12 ) | ( padThumbhashRound ( 31 * $lScale ) << 18 ) | ( (int) $hasAlpha << 23 );
    $header16  = ( $landscape ? $ly : $lx ) | ( padThumbhashRound ( 63 * $pScale ) << 3 ) | ( padThumbhashRound ( 63 * $qScale ) << 9 ) | ( (int) $landscape << 15 );
    $hash      = [ $header24 & 255, ( $header24 >> 8 ) & 255, ( $header24 >> 16 ) & 255, $header16 & 255, ( $header16 >> 8 ) & 255 ];
    $start     = $hasAlpha ? 6 : 5;
    $index     = 0;

    if ( $hasAlpha )
      $hash [] = ( padThumbhashRound ( 15 * $aDc ) | ( padThumbhashRound ( 15 * $aScale ) << 4 ) ) & 255;

    foreach ( $hasAlpha ? [ $lAc, $pAc, $qAc, $aAc ] : [ $lAc, $pAc, $qAc ] as $terms )
      foreach ( $terms as $f ) {
        $at          = $start + ( $index >> 1 );
        $hash [$at]  = ( ( $hash [$at] ?? 0 ) | ( padThumbhashRound ( 15 * $f ) << ( ( $index & 1 ) << 2 ) ) ) & 255;
        $index++;
      }

    return implode ( '', array_map ( 'chr', $hash ) );

  }

  // The shape a hash holds, width over height - the reference's thumbHashToApproximateAspectRatio.

  function padThumbhashRatio ( $hash ) {

    $alpha     = ord ( $hash [2] ) & 0x80;
    $landscape = ord ( $hash [4] ) & 0x80;
    $lx        = $landscape ? ( $alpha ? 5 : 7 ) : ord ( $hash [3] ) & 7;
    $ly        = $landscape ? ord ( $hash [3] ) & 7 : ( $alpha ? 5 : 7 );

    return $ly ? $lx / $ly : 0;

  }

  function padThumbhashDecode ( $hash ) {

    $hash = (string) $hash;

    if ( strlen ( $hash ) < 5 )
      return [ 0, 0, [] ];

    $byte = fn ( $k ) => isset ( $hash [$k] ) ? ord ( $hash [$k] ) : 0;

    $header24  = $byte ( 0 ) | ( $byte ( 1 ) << 8 ) | ( $byte ( 2 ) << 16 );
    $header16  = $byte ( 3 ) | ( $byte ( 4 ) << 8 );
    $lDc       = ( $header24 & 63 ) / 63;
    $pDc       = ( ( $header24 >> 6 ) & 63 ) / 31.5 - 1;
    $qDc       = ( ( $header24 >> 12 ) & 63 ) / 31.5 - 1;
    $lScale    = ( ( $header24 >> 18 ) & 31 ) / 31;
    $hasAlpha  = $header24 >> 23;
    $pScale    = ( ( $header16 >> 3 ) & 63 ) / 63;
    $qScale    = ( ( $header16 >> 9 ) & 63 ) / 63;
    $landscape = $header16 >> 15;
    $lx        = max ( 3, $landscape ? ( $hasAlpha ? 5 : 7 ) : $header16 & 7 );
    $ly        = max ( 3, $landscape ? $header16 & 7 : ( $hasAlpha ? 5 : 7 ) );
    $aDc       = $hasAlpha ? ( $byte ( 5 ) & 15 ) / 15 : 1;
    $aScale    = ( $byte ( 5 ) >> 4 ) / 15;
    $start     = $hasAlpha ? 6 : 5;
    $index     = 0;

    $channel = function ( $nx, $ny, $scale ) use ( $byte, $start, &$index ) {
      $ac = [];
      for ( $cy = 0; $cy < $ny; $cy++ )
        for ( $cx = $cy ? 0 : 1; $cx * $ny < $nx * ( $ny - $cy ); $cx++ ) {
          $ac [] = ( ( ( $byte ( $start + ( $index >> 1 ) ) >> ( ( $index & 1 ) << 2 ) ) & 15 ) / 7.5 - 1 ) * $scale;
          $index++;
        }
      return $ac;
    };

    $lAc = $channel ( $lx, $ly, $lScale );
    $pAc = $channel ( 3, 3, $pScale * 1.25 );
    $qAc = $channel ( 3, 3, $qScale * 1.25 );
    $aAc = $hasAlpha ? $channel ( 5, 5, $aScale ) : [];

    $ratio = padThumbhashRatio ( $hash );

    if ( ! $ratio )
      return [ 0, 0, [] ];

    $w    = padThumbhashRound ( $ratio > 1 ? 32 : 32 * $ratio );
    $h    = padThumbhashRound ( $ratio > 1 ? 32 / $ratio : 32 );
    $rgba = [];
    $nx   = max ( $lx, $hasAlpha ? 5 : 3 );
    $ny   = max ( $ly, $hasAlpha ? 5 : 3 );
    $cut  = fn ( $v ) => (int) floor ( max ( 0, 255 * min ( 1, $v ) ) );

    for ( $y = 0; $y < $h; $y++ )
      for ( $x = 0; $x < $w; $x++ ) {

        $l = $lDc;
        $p = $pDc;
        $q = $qDc;
        $a = $aDc;

        $fx = [];
        $fy = [];

        for ( $cx = 0; $cx < $nx; $cx++ )
          $fx [$cx] = cos ( M_PI / $w * ( $x + 0.5 ) * $cx );

        for ( $cy = 0; $cy < $ny; $cy++ )
          $fy [$cy] = cos ( M_PI / $h * ( $y + 0.5 ) * $cy );

        for ( $cy = 0, $j = 0; $cy < $ly; $cy++ ) {
          $fy2 = $fy [$cy] * 2;
          for ( $cx = $cy ? 0 : 1; $cx * $ly < $lx * ( $ly - $cy ); $cx++, $j++ )
            $l += $lAc [$j] * $fx [$cx] * $fy2;
        }

        for ( $cy = 0, $j = 0; $cy < 3; $cy++ ) {
          $fy2 = $fy [$cy] * 2;
          for ( $cx = $cy ? 0 : 1; $cx < 3 - $cy; $cx++, $j++ ) {
            $f  = $fx [$cx] * $fy2;
            $p += $pAc [$j] * $f;
            $q += $qAc [$j] * $f;
          }
        }

        if ( $hasAlpha )
          for ( $cy = 0, $j = 0; $cy < 5; $cy++ ) {
            $fy2 = $fy [$cy] * 2;
            for ( $cx = $cy ? 0 : 1; $cx < 5 - $cy; $cx++, $j++ )
              $a += $aAc [$j] * $fx [$cx] * $fy2;
          }

        $b = $l - 2 / 3 * $p;
        $r = ( 3 * $l - $b + $q ) / 2;
        $g = $r - $q;

        array_push ( $rgba, $cut ( $r ), $cut ( $g ), $cut ( $b ), $cut ( $a ) );

      }

    return [ $w, $h, $rgba ];

  }

  // The RGBA of a GD picture, row by row - its opacity of 0 to 127 as the 0 to 255 GD's PNG
  // writer makes of it, so the hash is of the picture the browser is sent.

  function padThumbhashPixels ( $image ) {

    $w    = imagesx ( $image );
    $h    = imagesy ( $image );
    $rgba = [];

    for ( $y = 0; $y < $h; $y++ )
      for ( $x = 0; $x < $w; $x++ ) {
        $c = imagecolorat ( $image, $x, $y );
        $t = ( $c >> 24 ) & 127;
        array_push ( $rgba, ( $c >> 16 ) & 255, ( $c >> 8 ) & 255, $c & 255, 255 - ( ( $t << 1 ) + ( $t >> 6 ) ) );
      }

    return $rgba;

  }

?>
