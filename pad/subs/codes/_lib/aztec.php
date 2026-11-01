<?php

  // Aztec Code (ISO/IEC 24778), the square code of tickets and boarding passes: a bullseye in
  // the middle, the size of the symbol in a ring of mode message around it, and the data in
  // layers of two modules wound around them - compact symbols of 1 to 4 layers, 15 by 15 up to
  // 27 by 27 modules, full ones of 4 to 32 layers up to 151 by 151, with a reference grid
  // every 16 modules. No quiet zone is needed; two modules are drawn, as for Data Matrix.
  //
  //   {aztec 'Code 2D!'/}
  //   {barcode:aztec $ticket, size=200, ecc=33/}
  //
  // padAztec         the SVG, or '' when the text does not fit in the largest symbol
  // padAztecMatrix   the symbol: rows of TRUE (dark) and FALSE (light), no quiet zone
  //
  // The text is encoded in the five modes of the standard - upper, lower, mixed (control
  // characters and @ \ ^ _ ` | ~), punctuation and digits, latched to and shifted into at the
  // least number of bits for the whole text - and the bytes no mode has, those of a text's
  // UTF-8 beyond ASCII among them, after a binary shift; a text with such a byte that is UTF-8
  // starts with the ECI designator of UTF-8, FLG(2) 26 - a reader without it reads the bytes
  // as ISO/IEC 8859-1. The bits are cut into codewords of 6, 8, 10 or 12 bits by the number of
  // layers, a codeword of all ones or all zeros but its last bit stuffed with the other bit,
  // and the error correction is Reed-Solomon over GF(2^6) x^6+x+1, GF(2^8) 0x12D, GF(2^10)
  // x^10+x^3+1 or GF(2^12) 0x1069, the mode message's over GF(16) x^4+x+1. The symbol is the
  // smallest that leaves room for the error correction asked - by default the standard's
  // recommendation, 23% of the codewords and 3 more.

  // The characters of the modes: their value in the mode. Mixed holds the control characters
  // 1 to 13 and 27 to 31 and DEL; punctuation two pairs of two characters besides.

  const PAD_AZTEC_U = 0;
  const PAD_AZTEC_L = 1;
  const PAD_AZTEC_M = 2;
  const PAD_AZTEC_P = 3;
  const PAD_AZTEC_D = 4;

  // The latches from a mode to another: [ bits, value ] - L to U goes through D, a mode to P
  // through M, D to anything through U.

  const PAD_AZTEC_LATCH = [
    [ [ 0, 0 ], [ 5, 28 ], [ 5, 29 ], [ 10, 29 << 5 | 30 ], [ 5, 30 ] ],
    [ [ 9, 30 << 4 | 14 ], [ 0, 0 ], [ 5, 29 ], [ 10, 29 << 5 | 30 ], [ 5, 30 ] ],
    [ [ 5, 29 ], [ 5, 28 ], [ 0, 0 ], [ 5, 30 ], [ 10, 29 << 5 | 30 ] ],
    [ [ 5, 31 ], [ 10, 31 << 5 | 28 ], [ 10, 31 << 5 | 29 ], [ 0, 0 ], [ 10, 31 << 5 | 30 ] ],
    [ [ 4, 14 ], [ 9, 14 << 5 | 28 ], [ 9, 14 << 5 | 29 ], [ 14, 14 << 10 | 29 << 5 | 30 ], [ 0, 0 ] ] ];

  // The width of a codeword by the number of layers, and the field of its error correction.

  const PAD_AZTEC_FIELDS = [ 4 => 0x13, 6 => 0x43, 8 => 0x12D, 10 => 0x409, 12 => 0x1069 ];

  function padAztec ( $text, $size = 160, $title = '', $dark = '#000', $light = '#fff', $ecc = 23 ) {

    $matrix = padAztecMatrix ( (string) $text, $ecc );

    if ( ! $matrix )
      return '';

    return padCodeMatrix ( $matrix, 2, $size, ( $title !== '' ) ? $title : (string) $text, $dark, $light, 'pad-aztec' );

  }

  // The value of each character in each mode, a table built once: [ mode ] [ byte ] => value.

  function padAztecChars () {

    static $chars = NULL;

    if ( $chars !== NULL )
      return $chars;

    $chars = [ [], [], [], [], [] ];

    $chars [PAD_AZTEC_U] [32] = $chars [PAD_AZTEC_L] [32] = $chars [PAD_AZTEC_M] [32] = $chars [PAD_AZTEC_D] [32] = 1;

    for ( $c = 0; $c < 26; $c++ ) {
      $chars [PAD_AZTEC_U] [65 + $c] = 2 + $c;
      $chars [PAD_AZTEC_L] [97 + $c] = 2 + $c;
    }

    for ( $c = 0; $c < 10; $c++ )
      $chars [PAD_AZTEC_D] [48 + $c] = 2 + $c;

    $chars [PAD_AZTEC_D] [44] = 12;
    $chars [PAD_AZTEC_D] [46] = 13;

    foreach ( array_merge ( range ( 1, 13 ), range ( 27, 31 ), [ 64, 92, 94, 95, 96, 124, 126, 127 ] ) as $k => $byte )
      $chars [PAD_AZTEC_M] [$byte] = 2 + $k;

    foreach ( str_split ( "\r\0\0\0\0!\"#$%&'()*+,-./:;<=>?[]{}" ) as $k => $char )
      if ( $char !== "\0" )
        $chars [PAD_AZTEC_P] [ ord ( $char ) ] = 1 + $k;

    return $chars;

  }

  // The bits of the text: [ [ count, value ], ... ], the fewest the modes allow. A walk from the
  // end to the start keeps, for every place and mode, the least number of bits that encode
  // the rest of the text from there; a run of bytes no mode has is one step, a binary shift.

  function padAztecBits ( $text ) {

    $chars  = padAztecChars ();
    $length = strlen ( $text );
    $width  = [ 5, 5, 5, 5, 4 ];
    $pairs  = [ "\r\n" => 2, '. ' => 3, ', ' => 4, ': ' => 5 ];
    $bytes  = array_map ( 'ord', str_split ( $text ) );
    $any    = fn ( $byte ) => isset ( $chars [0] [$byte] ) or isset ( $chars [1] [$byte] ) or isset ( $chars [2] [$byte] )
                              or isset ( $chars [3] [$byte] ) or isset ( $chars [4] [$byte] );

    // The steps from a place in a mode: [ bits, codes, next place, mode after ], codes the
    // [ count, value ] the step writes - the character in the mode, after a shift, a pair of
    // punctuation, a binary run - and the latches tried when the step needs another mode.

    $cost = array_fill ( 0, $length + 1, array_fill ( 0, 5, INF ) );
    $how  = [];
    $runs = [ $length => 0 ];

    for ( $i = $length - 1; $i >= 0; $i-- )
      $runs [$i] = $any ( $bytes [$i] ) ? 0 : 1 + $runs [$i + 1];

    for ( $mode = 0; $mode < 5; $mode++ )
      $cost [$length] [$mode] = 0;

    for ( $i = $length - 1; $i >= 0; $i-- ) {

      $byte  = $bytes [$i];
      $steps = array_fill ( 0, 5, [] );

      if ( $runs [$i] ) {

        $run = $runs [$i];

        foreach ( [ PAD_AZTEC_U, PAD_AZTEC_L, PAD_AZTEC_M ] as $mode )
          $steps [$mode] [] = [ padAztecBinaryBits ( $run ), [ [ 'binary', $i, $run ] ], $i + $run, $mode ];

      } else {

        $pair = ( $i + 1 < $length ) ? ( $pairs [ substr ( $text, $i, 2 ) ] ?? 0 ) : 0;

        for ( $mode = 0; $mode < 5; $mode++ ) {

          if ( isset ( $chars [$mode] [$byte] ) )
            $steps [$mode] [] = [ $width [$mode], [ [ $width [$mode], $chars [$mode] [$byte] ] ], $i + 1, $mode ];

          if ( $mode != PAD_AZTEC_P ) {
            if ( isset ( $chars [PAD_AZTEC_P] [$byte] ) )
              $steps [$mode] [] = [ $width [$mode] + 5, [ [ $width [$mode], 0 ], [ 5, $chars [PAD_AZTEC_P] [$byte] ] ], $i + 1, $mode ];
            if ( $pair )
              $steps [$mode] [] = [ $width [$mode] + 5, [ [ $width [$mode], 0 ], [ 5, $pair ] ], $i + 2, $mode ];
          } elseif ( $pair )
            $steps [$mode] [] = [ 5, [ [ 5, $pair ] ], $i + 2, $mode ];

          if ( ( $mode == PAD_AZTEC_L or $mode == PAD_AZTEC_D ) and isset ( $chars [PAD_AZTEC_U] [$byte] ) )
            $steps [$mode] [] = [ $width [$mode] + 5, [ [ $width [$mode], $mode == PAD_AZTEC_L ? 28 : 15 ], [ 5, $chars [PAD_AZTEC_U] [$byte] ] ], $i + 1, $mode ];

        }

      }

      // The least of the steps in each mode, then of a latch to another mode and its steps.

      $stay = array_fill ( 0, 5, [ INF, NULL ] );

      for ( $mode = 0; $mode < 5; $mode++ )
        foreach ( $steps [$mode] as $step )
          if ( $step [0] + $cost [ $step [2] ] [ $step [3] ] < $stay [$mode] [0] )
            $stay [$mode] = [ $step [0] + $cost [ $step [2] ] [ $step [3] ], $step ];

      for ( $mode = 0; $mode < 5; $mode++ ) {
        $cost [$i] [$mode] = $stay [$mode] [0];
        $how  [$i] [$mode] = [ $mode, $stay [$mode] [1] ];
        for ( $to = 0; $to < 5; $to++ )
          if ( $to != $mode and PAD_AZTEC_LATCH [$mode] [$to] [0] + $stay [$to] [0] < $cost [$i] [$mode] ) {
            $cost [$i] [$mode] = PAD_AZTEC_LATCH [$mode] [$to] [0] + $stay [$to] [0];
            $how  [$i] [$mode] = [ $to, $stay [$to] [1] ];
          }
      }

    }

    // The walk forward along the least steps, from upper case, which a symbol starts in.

    $codes = [];
    $mode  = PAD_AZTEC_U;

    if ( preg_match ( '/[\x80-\xff]/', $text ) and preg_match ( '//u', $text ) )
      array_push ( $codes, [ 5, 0 ], [ 5, 0 ], [ 3, 2 ], [ 4, 4 ], [ 4, 8 ] );

    for ( $i = 0; $i < $length; ) {

      list ( $to, $step ) = $how [$i] [$mode];

      if ( $to != $mode ) {
        $codes [] = PAD_AZTEC_LATCH [$mode] [$to];
        $mode     = $to;
      }

      foreach ( $step [1] as $code )
        if ( $code [0] === 'binary' )
          array_push ( $codes, ...padAztecBinary ( $bytes, $code [1], $code [2] ) );
        else
          $codes [] = $code;

      $i    = $step [2];
      $mode = $step [3];

    }

    return $codes;

  }

  // A run of bytes after a binary shift: up to 31 with a length of 5 bits, up to 62 as two
  // such shifts, more with a length of 0 and then 11 bits of the length less 31 - a run of
  // more than 2078 cut into such runs.

  function padAztecBinaryBits ( $run ) {

    $bits = 0;

    for ( $left = $run; $left > 0; $left -= $take ) {
      $take  = $left <= 62 ? min ( $left, 31 ) : min ( $left, 2078 );
      $bits += 5 + ( $take <= 31 ? 5 : 16 ) + 8 * $take;
    }

    return $bits;

  }

  function padAztecBinary ( $bytes, $from, $run ) {

    $codes = [];

    for ( $left = $run; $left > 0; $left -= $take ) {
      $take     = $left <= 62 ? min ( $left, 31 ) : min ( $left, 2078 );
      $codes [] = [ 5, 31 ];
      $codes [] = $take <= 31 ? [ 5, $take ] : [ 16, $take - 31 ];
      for ( $k = 0; $k < $take; $k++ )
        $codes [] = [ 8, $bytes [$from++] ];
    }

    return $codes;

  }

  // The symbol, or NULL when the text does not fit in a full symbol of 32 layers.

  function padAztecMatrix ( $text, $ecc = 23 ) {

    $bits = '';

    foreach ( padAztecBits ( $text ) as list ( $count, $value ) )
      $bits .= str_pad ( decbin ( $value ), $count, '0', STR_PAD_LEFT );

    // The compact symbols first, then the full ones from 4 layers - a full symbol of 1 to 3
    // layers is as large as a compact one of 2 to 4 and holds less.

    foreach ( array_merge ( [ [ TRUE, 1 ], [ TRUE, 2 ], [ TRUE, 3 ], [ TRUE, 4 ] ], array_map ( fn ( $l ) => [ FALSE, $l ], range ( 4, 32 ) ) ) as list ( $compact, $layers ) ) {

      $word    = $layers <= 2 ? 6 : ( $layers <= 8 ? 8 : ( $layers <= 22 ? 10 : 12 ) );
      $total   = ( ( $compact ? 88 : 112 ) + 16 * $layers ) * $layers;
      $words   = intdiv ( $total, $word );
      $stuffed = padAztecStuff ( $bits, $word );
      $data    = intdiv ( strlen ( $stuffed ), $word );

      if ( $data > ( $compact ? 64 : 2048 ) or $words - $data < (int) ceil ( $words * $ecc / 100 ) + 3 )
        continue;

      $codewords = array_map ( 'bindec', str_split ( $stuffed, $word ) );
      $codewords = array_merge ( $codewords, padAztecCheck ( $codewords, $words - $data, $word ) );

      $message = str_repeat ( '0', $total % $word );
      foreach ( $codewords as $codeword )
        $message .= str_pad ( decbin ( $codeword ), $word, '0', STR_PAD_LEFT );

      return padAztecDraw ( $compact, $layers, $data, $message );

    }

    return NULL;

  }

  // The bits cut into codewords: a codeword whose first bits are all ones or all zeros gets
  // the other bit last, and the bit it pushed out begins the next one; the last codeword is
  // filled up with ones.

  function padAztecStuff ( $bits, $word ) {

    $out    = '';
    $length = strlen ( $bits );

    for ( $i = 0; $i < $length; ) {

      $piece = str_pad ( substr ( $bits, $i, $word - 1 ), $word - 1, '1' );

      if ( $piece === str_repeat ( '1', $word - 1 ) ) {
        $out .= $piece . '0';
        $i   += $word - 1;
      } elseif ( $piece === str_repeat ( '0', $word - 1 ) ) {
        $out .= $piece . '1';
        $i   += $word - 1;
      } else {
        $out .= $piece . ( $i + $word - 1 < $length ? $bits [$i + $word - 1] : '1' );
        $i   += $word;
      }

    }

    return $out;

  }

  // The Reed-Solomon check words of the data words, in GF(2^$word): the remainder of the data
  // times x^n divided by the generator, the product of (x - a^i) for i 1 to n.

  function padAztecCheck ( $data, $count, $word ) {

    $poly  = PAD_AZTEC_FIELDS [$word];
    $size  = 1 << $word;
    $exp   = [];
    $log   = [];
    $value = 1;

    for ( $i = 0; $i < $size - 1; $i++ ) {
      $exp [$i]     = $value;
      $log [$value] = $i;
      $value      <<= 1;
      if ( $value & $size )
        $value ^= $poly;
    }

    $multiply = fn ( $a, $b ) => ( $a and $b ) ? $exp [ ( $log [$a] + $log [$b] ) % ( $size - 1 ) ] : 0;

    $generator = [ 1 ];

    for ( $i = 1; $i <= $count; $i++ ) {
      $next = array_fill ( 0, count ( $generator ) + 1, 0 );
      foreach ( $generator as $k => $coefficient ) {
        $next [$k]     ^= $coefficient;
        $next [$k + 1] ^= $multiply ( $coefficient, $exp [ $i % ( $size - 1 ) ] );
      }
      $generator = $next;
    }

    $remainder = array_fill ( 0, $count, 0 );

    foreach ( $data as $codeword ) {
      $factor = $codeword ^ $remainder [0];
      array_shift ( $remainder );
      $remainder [] = 0;
      for ( $k = 0; $k < $count; $k++ )
        $remainder [$k] ^= $multiply ( $generator [$k + 1], $factor );
    }

    return $remainder;

  }

  // The symbol drawn: the layers of data from the outside in - each layer two modules deep,
  // its four sides in turn from the top left going right, then down, left and up - around the
  // mode message, the bullseye and its orientation marks, and in a full symbol the reference
  // grid, which the layers step over.

  function padAztecDraw ( $compact, $layers, $data, $message ) {

    $base = ( $compact ? 11 : 14 ) + 4 * $layers;
    $map  = range ( 0, $base - 1 );
    $side = $base;

    if ( ! $compact ) {
      $side   = $base + 1 + 2 * intdiv ( intdiv ( $base, 2 ) - 1, 15 );
      $centre = intdiv ( $base, 2 );
      $middle = intdiv ( $side, 2 );
      for ( $i = 0; $i < $centre; $i++ ) {
        $offset                 = $i + intdiv ( $i, 15 );
        $map [$centre - $i - 1] = $middle - $offset - 1;
        $map [$centre + $i]     = $middle + $offset + 1;
      }
    }

    $m   = array_fill ( 0, $side, array_fill ( 0, $side, FALSE ) );
    $set = function ( $x, $y ) use ( &$m ) { $m [$y] [$x] = TRUE; };

    for ( $i = 0, $row = 0; $i < $layers; $i++ ) {
      $size = ( $layers - $i ) * 4 + ( $compact ? 9 : 12 );
      for ( $j = 0; $j < $size; $j++ )
        for ( $k = 0; $k < 2; $k++ ) {
          $at = $row + 2 * $j + $k;
          if ( $message [$at] == '1' )                 $set ( $map [$i * 2 + $k], $map [$i * 2 + $j] );
          if ( $message [$at + 2 * $size] == '1' )     $set ( $map [$i * 2 + $j], $map [$base - 1 - $i * 2 - $k] );
          if ( $message [$at + 4 * $size] == '1' )     $set ( $map [$base - 1 - $i * 2 - $k], $map [$base - 1 - $i * 2 - $j] );
          if ( $message [$at + 6 * $size] == '1' )     $set ( $map [$base - 1 - $i * 2 - $j], $map [$i * 2 + $k] );
        }
      $row += 8 * $size;
    }

    // The mode message: the layers less one and the data codewords less one, with its own
    // error correction in codewords of four bits.

    $mode = $compact
      ? str_pad ( decbin ( $layers - 1 ), 2, '0', STR_PAD_LEFT ) . str_pad ( decbin ( $data - 1 ), 6, '0', STR_PAD_LEFT )
      : str_pad ( decbin ( $layers - 1 ), 5, '0', STR_PAD_LEFT ) . str_pad ( decbin ( $data - 1 ), 11, '0', STR_PAD_LEFT );

    $words = array_map ( 'bindec', str_split ( $mode, 4 ) );

    foreach ( padAztecCheck ( $words, $compact ? 5 : 6, 4 ) as $check )
      $mode .= str_pad ( decbin ( $check ), 4, '0', STR_PAD_LEFT );

    $c = intdiv ( $side, 2 );

    if ( $compact )
      for ( $i = 0; $i < 7; $i++ ) {
        $offset = $c - 3 + $i;
        if ( $mode [$i] == '1' )      $set ( $offset, $c - 5 );
        if ( $mode [$i + 7] == '1' )  $set ( $c + 5, $offset );
        if ( $mode [20 - $i] == '1' ) $set ( $offset, $c + 5 );
        if ( $mode [27 - $i] == '1' ) $set ( $c - 5, $offset );
      }
    else
      for ( $i = 0; $i < 10; $i++ ) {
        $offset = $c - 5 + $i + intdiv ( $i, 5 );
        if ( $mode [$i] == '1' )      $set ( $offset, $c - 7 );
        if ( $mode [$i + 10] == '1' ) $set ( $c + 7, $offset );
        if ( $mode [29 - $i] == '1' ) $set ( $offset, $c + 7 );
        if ( $mode [39 - $i] == '1' ) $set ( $c - 7, $offset );
      }

    // The bullseye - rings of dark and light around a dark centre, 2 or 3 dark ones - and the
    // orientation marks at its corners.

    $eye = $compact ? 5 : 7;

    for ( $i = 0; $i < $eye; $i += 2 )
      for ( $j = $c - $i; $j <= $c + $i; $j++ ) {
        $set ( $j, $c - $i );
        $set ( $j, $c + $i );
        $set ( $c - $i, $j );
        $set ( $c + $i, $j );
      }

    foreach ( [ [ -$eye, -$eye ], [ -$eye + 1, -$eye ], [ -$eye, -$eye + 1 ], [ $eye, -$eye ], [ $eye, -$eye + 1 ], [ $eye, $eye - 1 ] ] as list ( $dx, $dy ) )
      $set ( $c + $dx, $c + $dy );

    // The reference grid of a full symbol: every 16th row and column from the centre, dark and
    // light in turn.

    if ( ! $compact )
      for ( $i = 0, $j = 0; $i < intdiv ( $base, 2 ) - 1; $i += 15, $j += 16 )
        for ( $k = $c & 1; $k < $side; $k += 2 ) {
          $set ( $c - $j, $k );
          $set ( $c + $j, $k );
          $set ( $k, $c - $j );
          $set ( $k, $c + $j );
        }

    return $m;

  }

?>
