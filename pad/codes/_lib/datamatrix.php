<?php

  // Data Matrix, the square code of ISO/IEC 16022 - ECC 200 - that parts, letters and medicine
  // packs carry: any text, in the smallest of the 24 square symbols that holds it, 10 by 10
  // modules up to 144 by 144. Its finder is a solid L along the left and the bottom of every
  // data region, a dotted line along the top and the right.
  //
  //   {datamatrix 'PAD-2026-0042'/}
  //   {barcode:datamatrix $serial, size=120/}
  //
  // padDataMatrix          the SVG, or '' when the text does not fit in a 144 by 144 symbol
  // padDataMatrixMatrix    the symbol: rows of TRUE (dark) and FALSE (light), no quiet zone
  //
  // The text is encoded in the ASCII encodation: two digits to a codeword, a character of
  // ASCII to one, a byte above it - of the text's UTF-8 - to two, after the upper shift. The
  // codewords that are left are padding, the first 129 and the rest scrambled as the standard
  // says; the error correction is Reed-Solomon over GF(256) of the polynomial 0x12D, a block
  // of it for the small symbols and the codewords spread over 2 to 10 blocks for the large.

  // The square symbols: [ modules per side, modules per side of a data region, data regions
  // per side, data codewords, error correction codewords, blocks ].

  const PAD_DATAMATRIX_SIZES = [
    [  10,  8, 1,    3,   5,  1 ], [  12, 10, 1,    5,   7,  1 ], [  14, 12, 1,    8,  10,  1 ],
    [  16, 14, 1,   12,  12,  1 ], [  18, 16, 1,   18,  14,  1 ], [  20, 18, 1,   22,  18,  1 ],
    [  22, 20, 1,   30,  20,  1 ], [  24, 22, 1,   36,  24,  1 ], [  26, 24, 1,   44,  28,  1 ],
    [  32, 14, 2,   62,  36,  1 ], [  36, 16, 2,   86,  42,  1 ], [  40, 18, 2,  114,  48,  1 ],
    [  44, 20, 2,  144,  56,  1 ], [  48, 22, 2,  174,  68,  1 ], [  52, 24, 2,  204,  84,  2 ],
    [  64, 14, 4,  280, 112,  2 ], [  72, 16, 4,  368, 144,  4 ], [  80, 18, 4,  456, 192,  4 ],
    [  88, 20, 4,  576, 224,  4 ], [  96, 22, 4,  696, 272,  4 ], [ 104, 24, 4,  816, 336,  6 ],
    [ 120, 18, 6, 1050, 408,  6 ], [ 132, 20, 6, 1304, 496,  8 ], [ 144, 22, 6, 1558, 620, 10 ] ];

  // The SVG: the symbol in a light square with a quiet zone of two modules around it - the
  // standard asks one - drawn as padCodeMatrix draws a QR code.

  function padDataMatrix ( $text, $size = 160, $title = '', $dark = '#000', $light = '#fff' ) {

    $matrix = padDataMatrixMatrix ( (string) $text );

    if ( ! $matrix )
      return '';

    return padCodeMatrix ( $matrix, 2, $size, ( $title !== '' ) ? $title : (string) $text, $dark, $light, 'pad-datamatrix' );

  }

  // The codewords of the ASCII encodation.

  function padDataMatrixAscii ( $text ) {

    $codewords = [];
    $length    = strlen ( $text );

    for ( $i = 0; $i < $length; $i++ ) {
      $byte = ord ( $text [$i] );
      if ( $i + 1 < $length and ctype_digit ( $text [$i] ) and ctype_digit ( $text [$i + 1] ) ) {
        $codewords [] = 130 + (int) substr ( $text, $i, 2 );
        $i++;
      } elseif ( $byte < 128 )
        $codewords [] = $byte + 1;
      else
        array_push ( $codewords, 235, $byte - 127 );
    }

    return $codewords;

  }

  // The symbol: the smallest square that holds the codewords, padded, the error correction
  // added and interleaved, placed in the data regions and framed by the finder of each.
  // NULL when not even the largest holds the text.

  function padDataMatrixMatrix ( $text ) {

    $data = padDataMatrixAscii ( $text );

    foreach ( PAD_DATAMATRIX_SIZES as $symbol )
      if ( count ( $data ) <= $symbol [3] )
        break;

    list ( $side, $region, $regions, $capacity, $ecc, $blocks ) = $symbol;

    if ( count ( $data ) > $capacity )
      return NULL;

    if ( count ( $data ) < $capacity )
      $data [] = 129;

    while ( count ( $data ) < $capacity ) {
      $pad     = 129 + ( 149 * ( count ( $data ) + 1 ) ) % 253 + 1;
      $data [] = ( $pad <= 254 ) ? $pad : $pad - 254;
    }

    $codewords = padDataMatrixInterleave ( $data, $ecc, $blocks );
    $map       = padDataMatrixPlace ( $region * $regions, $region * $regions );
    $matrix    = array_fill ( 0, $side, array_fill ( 0, $side, FALSE ) );

    for ( $y = 0; $y < $side; $y++ )
      for ( $x = 0; $x < $side; $x++ ) {

        $ly = $y % ( $region + 2 );
        $lx = $x % ( $region + 2 );

        if ( $lx == 0 or $ly == $region + 1 )
          $matrix [$y] [$x] = TRUE;
        elseif ( $ly == 0 )
          $matrix [$y] [$x] = ( $lx % 2 == 0 );
        elseif ( $lx == $region + 1 )
          $matrix [$y] [$x] = ( $ly % 2 == 1 );
        else {
          $at = $map [ intdiv ( $y, $region + 2 ) * $region + $ly - 1 ] [ intdiv ( $x, $region + 2 ) * $region + $lx - 1 ];
          $matrix [$y] [$x] = ( $at == 1 || ( $at >= 10 && ( $codewords [ intdiv ( $at, 10 ) - 1 ] >> ( 8 - $at % 10 ) & 1 ) ) );
        }

      }

    return $matrix;

  }

  // The data with its error correction: the data dealt over the blocks in turn, the
  // Reed-Solomon codewords of each block put behind its data, and the blocks read back the
  // way they were dealt - the first codeword of every block, then the second, and so on. In
  // the 144 by 144 symbol the last two of its ten blocks have a data codeword less, so there
  // their error correction starts a turn sooner: dealt as two separate turns, data and then
  // error correction, the code read as nothing.

  function padDataMatrixInterleave ( $data, $ecc, $blocks ) {

    $per       = intdiv ( $ecc, $blocks );
    $generator = padDataMatrixGenerator ( $per );
    $dealt     = [];

    for ( $block = 0; $block < $blocks; $block++ ) {

      $own       = [];
      $remainder = array_fill ( 0, $per, 0 );

      for ( $i = $block; $i < count ( $data ); $i += $blocks ) {
        $own []    = $data [$i];
        $factor    = $data [$i] ^ $remainder [0];
        array_shift ( $remainder );
        $remainder [] = 0;
        foreach ( $generator as $k => $coefficient )
          $remainder [$k] ^= padDataMatrixMultiply ( $factor, $coefficient );
      }

      $dealt [] = array_merge ( $own, $remainder );

    }

    $codewords = [];

    for ( $turn = 0; $turn < count ( $dealt [0] ); $turn++ )
      foreach ( $dealt as $block )
        if ( isset ( $block [$turn] ) )
          $codewords [] = $block [$turn];

    return $codewords;

  }

  // The generator of $degree error correction codewords, (x - 2)(x - 2^2) ... (x - 2^degree)
  // multiplied out, its coefficients after the leading 1, highest first.

  function padDataMatrixGenerator ( $degree ) {

    static $generators = [];

    if ( isset ( $generators [$degree] ) )
      return $generators [$degree];

    $poly = [ 1 ];

    for ( $i = 1, $root = 2; $i <= $degree; $i++, $root = padDataMatrixMultiply ( $root, 2 ) ) {
      $next = array_fill ( 0, count ( $poly ) + 1, 0 );
      foreach ( $poly as $k => $coefficient ) {
        $next [$k]     ^= $coefficient;
        $next [$k + 1] ^= padDataMatrixMultiply ( $coefficient, $root );
      }
      $poly = $next;
    }

    return $generators [$degree] = array_slice ( $poly, 1 );

  }

  // The product of two elements of GF(256) whose field polynomial is x^8 + x^5 + x^3 + x^2 + 1.

  function padDataMatrixMultiply ( $a, $b ) {

    $product = 0;

    while ( $b ) {
      if ( $b & 1 )
        $product ^= $a;
      $a <<= 1;
      if ( $a & 0x100 )
        $a ^= 0x12D;
      $b >>= 1;
    }

    return $product;

  }

  // Where each bit of each codeword goes in the data regions put together, $rows by $cols: the
  // placement of the standard's annex - the codewords as eight-module shapes along diagonals,
  // the shapes that run off an edge come back at the other side, with four corner cases. A
  // module holds 10 * codeword + bit, the codewords counted from 1 and the bits from 1, the
  // highest; 1 is a dark module of the fixed corner some sizes have left, 0 a light one.

  function padDataMatrixPlace ( $rows, $cols ) {

    $map = array_fill ( 0, $rows, array_fill ( 0, $cols, 0 ) );

    $module = function ( $row, $col, $chr, $bit ) use ( &$map, $rows, $cols ) {
      if ( $row < 0 ) { $row += $rows; $col += 4 - ( ( $rows + 4 ) % 8 ); }
      if ( $col < 0 ) { $col += $cols; $row += 4 - ( ( $cols + 4 ) % 8 ); }
      $map [$row] [$col] = 10 * $chr + $bit;
    };

    $utah = function ( $row, $col, $chr ) use ( $module ) {
      $module ( $row - 2, $col - 2, $chr, 1 ); $module ( $row - 2, $col - 1, $chr, 2 );
      $module ( $row - 1, $col - 2, $chr, 3 ); $module ( $row - 1, $col - 1, $chr, 4 ); $module ( $row - 1, $col, $chr, 5 );
      $module ( $row,     $col - 2, $chr, 6 ); $module ( $row,     $col - 1, $chr, 7 ); $module ( $row,     $col, $chr, 8 );
    };

    $corner = function ( $cells, $chr ) use ( $module ) {
      foreach ( $cells as $bit => list ( $row, $col ) )
        $module ( $row, $col, $chr, $bit + 1 );
    };

    $r = $rows;
    $c = $cols;

    $chr = 1;
    $row = 4;
    $col = 0;

    do {

      if ( $row == $r and $col == 0 )
        $corner ( [ [ $r-1, 0 ], [ $r-1, 1 ], [ $r-1, 2 ], [ 0, $c-2 ], [ 0, $c-1 ], [ 1, $c-1 ], [ 2, $c-1 ], [ 3, $c-1 ] ], $chr++ );
      if ( $row == $r - 2 and $col == 0 and $c % 4 )
        $corner ( [ [ $r-3, 0 ], [ $r-2, 0 ], [ $r-1, 0 ], [ 0, $c-4 ], [ 0, $c-3 ], [ 0, $c-2 ], [ 0, $c-1 ], [ 1, $c-1 ] ], $chr++ );
      if ( $row == $r - 2 and $col == 0 and $c % 8 == 4 )
        $corner ( [ [ $r-3, 0 ], [ $r-2, 0 ], [ $r-1, 0 ], [ 0, $c-2 ], [ 0, $c-1 ], [ 1, $c-1 ], [ 2, $c-1 ], [ 3, $c-1 ] ], $chr++ );
      if ( $row == $r + 4 and $col == 2 and ! ( $c % 8 ) )
        $corner ( [ [ $r-1, 0 ], [ $r-1, $c-1 ], [ 0, $c-3 ], [ 0, $c-2 ], [ 0, $c-1 ], [ 1, $c-3 ], [ 1, $c-2 ], [ 1, $c-1 ] ], $chr++ );

      do {
        if ( $row < $r and $col >= 0 and ! $map [$row] [$col] )
          $utah ( $row, $col, $chr++ );
        $row -= 2;
        $col += 2;
      } while ( $row >= 0 and $col < $c );

      $row += 1;
      $col += 3;

      do {
        if ( $row >= 0 and $col < $c and ! $map [$row] [$col] )
          $utah ( $row, $col, $chr++ );
        $row += 2;
        $col -= 2;
      } while ( $row < $r and $col >= 0 );

      $row += 3;
      $col += 1;

    } while ( $row < $r or $col < $c );

    if ( ! $map [$r - 1] [$c - 1] )
      $map [$r - 1] [$c - 1] = $map [$r - 2] [$c - 2] = 1;

    return $map;

  }

?>
