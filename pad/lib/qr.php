<?php

  // QR codes - the {qr} tag. The text is encoded the way ISO/IEC 18004 says, and the symbol
  // is written as inline SVG: no image library, no remote service, no JavaScript.
  //
  //   {qr 'https://example.com/order/42'}
  //   {qr $url, size=200, level='H', title='Open the order on your phone'}
  //
  // padQr          the SVG of a text, or '' when it does not fit in a version 40 symbol
  // padQrMatrix    the symbol itself: rows of TRUE (dark) and FALSE (light), no quiet zone
  //
  // The text is encoded in one mode - numeric for digits only, alphanumeric for the 45
  // characters of that mode (upper case, digits, space and $%*+-./:), else bytes of its
  // UTF-8 - in the smallest version that holds it at the level asked: L, M (default), Q
  // or H, which restore 7, 15, 25 and 30 percent of a damaged symbol. Of the eight masks
  // the one with the lowest penalty is taken, as a reader expects.

  const PAD_QR_ECC_PER_BLOCK = [
    'L' => [ 0, 7, 10, 15, 20, 26, 18, 20, 24, 30, 18, 20, 24, 26, 30, 22, 24, 28, 30, 28, 28, 28, 28, 30, 30, 26, 28, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30 ],
    'M' => [ 0, 10, 16, 26, 18, 24, 16, 18, 22, 22, 26, 30, 22, 22, 24, 24, 28, 28, 26, 26, 26, 26, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28, 28 ],
    'Q' => [ 0, 13, 22, 18, 26, 18, 24, 18, 22, 20, 24, 28, 26, 24, 20, 30, 24, 28, 28, 26, 30, 28, 30, 30, 30, 30, 28, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30 ],
    'H' => [ 0, 17, 28, 22, 16, 22, 28, 26, 26, 24, 28, 24, 28, 22, 24, 24, 30, 28, 28, 26, 28, 30, 24, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30 ] ];

  const PAD_QR_BLOCKS = [
    'L' => [ 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 4, 4, 4, 4, 4, 6, 6, 6, 6, 7, 8, 8, 9, 9, 10, 12, 12, 12, 13, 14, 15, 16, 17, 18, 19, 19, 20, 21, 22, 24, 25 ],
    'M' => [ 0, 1, 1, 1, 2, 2, 4, 4, 4, 5, 5, 5, 8, 9, 9, 10, 10, 11, 13, 14, 16, 17, 17, 18, 20, 21, 23, 25, 26, 28, 29, 31, 33, 35, 37, 38, 40, 43, 45, 47, 49 ],
    'Q' => [ 0, 1, 1, 2, 2, 4, 4, 6, 6, 8, 8, 8, 10, 12, 16, 12, 17, 16, 18, 21, 20, 23, 23, 25, 27, 29, 34, 34, 35, 38, 40, 43, 45, 48, 51, 53, 56, 59, 62, 65, 68 ],
    'H' => [ 0, 1, 1, 2, 4, 4, 4, 5, 6, 8, 8, 11, 11, 16, 16, 18, 16, 19, 21, 25, 25, 25, 34, 30, 32, 35, 37, 40, 42, 45, 48, 51, 54, 57, 60, 63, 66, 70, 74, 77, 81 ] ];

  const PAD_QR_ALNUM = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:';

  // The SVG: a light square with the quiet zone of four modules around the symbol, the dark
  // modules as one path of horizontal runs. The colours are fixed dark on light by default
  // - a reader needs the contrast, whatever the page's colour scheme - and color= and
  // background= change them.

  function padQr ( $text, $level = 'M', $size = 160, $title = '', $dark = '#000', $light = '#fff' ) {

    $matrix = padQrMatrix ( (string) $text, $level );

    if ( ! $matrix )
      return '';

    $n    = count ( $matrix );
    $full = $n + 8;
    $path = '';

    foreach ( $matrix as $y => $row )
      for ( $x = 0; $x < $n; $x++ )
        if ( $row [$x] ) {
          $run = 1;
          while ( $x + $run < $n and $row [$x + $run] )
            $run++;
          $path .= 'M' . ( $x + 4 ) . ',' . ( $y + 4 ) . "h{$run}v1h-{$run}z";
          $x += $run;
        }

    $title = ( $title !== '' ) ? $title : (string) $text;

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-qr" role="img" width="' . (int) $size . '" height="' . (int) $size . '"'
         . " viewBox=\"0 0 $full $full\" shape-rendering=\"crispEdges\">"
         . '<title>' . padQrAttr ( $title ) . '</title>'
         . '<rect width="100%" height="100%" fill="' . padQrAttr ( $light ) . '"/>'
         . '<path fill="' . padQrAttr ( $dark ) . "\" d=\"$path\"/></svg>";

  }

  function padQrAttr ( $text ) {

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // The mode a text is encoded in, and its bits after the mode indicator and the count.

  function padQrSegment ( $text ) {

    $bits = '';

    if ( $text !== '' and ctype_digit ( $text ) ) {
      foreach ( str_split ( $text, 3 ) as $group )
        $bits .= sprintf ( '%0' . ( [ 1 => 4, 2 => 7, 3 => 10 ] [strlen ( $group )] ) . 'b', (int) $group );
      return [ 'numeric', '0001', strlen ( $text ), $bits ];
    }

    if ( $text !== '' and strspn ( $text, PAD_QR_ALNUM ) == strlen ( $text ) ) {
      foreach ( str_split ( $text, 2 ) as $pair )
        if ( strlen ( $pair ) == 2 )
          $bits .= sprintf ( '%011b', strpos ( PAD_QR_ALNUM, $pair [0] ) * 45 + strpos ( PAD_QR_ALNUM, $pair [1] ) );
        else
          $bits .= sprintf ( '%06b', strpos ( PAD_QR_ALNUM, $pair ) );
      return [ 'alnum', '0010', strlen ( $text ), $bits ];
    }

    foreach ( str_split ( $text ) as $byte )
      $bits .= sprintf ( '%08b', ord ( $byte ) );

    return [ 'byte', '0100', strlen ( $text ), $bits ];

  }

  // The width of the count field: per mode, for versions 1-9, 10-26 and 27-40.

  function padQrCountBits ( $mode, $version ) {

    $widths = [ 'numeric' => [ 10, 12, 14 ], 'alnum' => [ 9, 11, 13 ], 'byte' => [ 8, 16, 16 ] ];

    return $widths [$mode] [ $version <= 9 ? 0 : ( $version <= 26 ? 1 : 2 ) ];

  }

  // The modules a version has for data and error correction together: everything but the
  // finders, separators, timing, alignment, format and version information.

  function padQrRawModules ( $version ) {

    $result = ( 16 * $version + 128 ) * $version + 64;

    if ( $version >= 2 ) {
      $align   = intdiv ( $version, 7 ) + 2;
      $result -= ( 25 * $align - 10 ) * $align - 55;
      if ( $version >= 7 )
        $result -= 36;
    }

    return $result;

  }

  function padQrDataCodewords ( $version, $level ) {

    return intdiv ( padQrRawModules ( $version ), 8 ) - PAD_QR_ECC_PER_BLOCK [$level] [$version] * PAD_QR_BLOCKS [$level] [$version];

  }

  // The symbol: the smallest version that holds the text, its codewords with their error
  // correction placed in the zigzag, the best mask applied. NULL when nothing holds it.

  function padQrMatrix ( $text, $level = 'M' ) {

    $level = strtoupper ( (string) $level );

    if ( ! isset ( PAD_QR_BLOCKS [$level] ) )
      $level = 'M';

    list ( $mode, $indicator, $count, $bits ) = padQrSegment ( $text );

    for ( $version = 1; $version <= 40; $version++ ) {
      $capacity = padQrDataCodewords ( $version, $level ) * 8;
      $used     = 4 + padQrCountBits ( $mode, $version ) + strlen ( $bits );
      if ( $used <= $capacity )
        break;
    }

    if ( $version > 40 )
      return NULL;

    $stream  = $indicator . sprintf ( '%0' . padQrCountBits ( $mode, $version ) . 'b', $count ) . $bits;
    $stream .= str_repeat ( '0', min ( 4, $capacity - strlen ( $stream ) ) );
    $stream .= str_repeat ( '0', ( 8 - strlen ( $stream ) % 8 ) % 8 );

    $data = array_map ( 'bindec', str_split ( $stream, 8 ) );

    for ( $pad = 0xEC; count ( $data ) < $capacity / 8; $pad ^= 0xEC ^ 0x11 )
      $data [] = $pad;

    $codewords = padQrInterleave ( $data, $version, $level );

    $size     = $version * 4 + 17;
    $modules  = array_fill ( 0, $size, array_fill ( 0, $size, FALSE ) );
    $function = $modules;

    padQrFunctionPatterns ( $modules, $function, $version, $level );
    padQrPlace ( $modules, $function, $codewords );

    $best = 0;
    $low  = PHP_INT_MAX;

    for ( $mask = 0; $mask < 8; $mask++ ) {
      padQrMask ( $modules, $function, $mask );
      padQrFormat ( $modules, $function, $level, $mask );
      $penalty = padQrPenalty ( $modules );
      if ( $penalty < $low ) {
        $low  = $penalty;
        $best = $mask;
      }
      padQrMask ( $modules, $function, $mask );
    }

    padQrMask   ( $modules, $function, $best );
    padQrFormat ( $modules, $function, $level, $best );

    return $modules;

  }

  // The data split in blocks, each with its Reed-Solomon codewords, and the blocks read
  // column by column - the data of all, then the error correction of all.

  function padQrInterleave ( $data, $version, $level ) {

    $blocks    = PAD_QR_BLOCKS [$level] [$version];
    $eccLength = PAD_QR_ECC_PER_BLOCK [$level] [$version];
    $raw       = intdiv ( padQrRawModules ( $version ), 8 );
    $short     = $blocks - $raw % $blocks;
    $shortLen  = intdiv ( $raw, $blocks );
    $divisor   = padQrDivisor ( $eccLength );
    $all       = [];
    $k         = 0;

    for ( $i = 0; $i < $blocks; $i++ ) {
      $length = $shortLen - $eccLength + ( $i < $short ? 0 : 1 );
      $part   = array_slice ( $data, $k, $length );
      $k     += $length;
      $ecc    = padQrRemainder ( $part, $divisor );
      if ( $i < $short )
        $part [] = 0;
      $all [] = array_merge ( $part, $ecc );
    }

    $result = [];

    for ( $i = 0; $i < count ( $all [0] ); $i++ )
      foreach ( $all as $j => $block )
        if ( $i != $shortLen - $eccLength or $j >= $short )
          $result [] = $block [$i];

    return $result;

  }

  // Multiplication in GF(2^8) over the polynomial x^8 + x^4 + x^3 + x^2 + 1.

  function padQrMultiply ( $x, $y ) {

    $z = 0;

    for ( $i = 7; $i >= 0; $i-- ) {
      $z  = ( $z << 1 ) ^ ( ( $z >> 7 ) * 0x11D );
      $z ^= ( ( $y >> $i ) & 1 ) * $x;
    }

    return $z & 0xFF;

  }

  function padQrDivisor ( $degree ) {

    $result = array_fill ( 0, $degree, 0 );
    $result [$degree - 1] = 1;
    $root = 1;

    for ( $i = 0; $i < $degree; $i++ ) {
      for ( $j = 0; $j < $degree; $j++ ) {
        $result [$j] = padQrMultiply ( $result [$j], $root );
        if ( $j + 1 < $degree )
          $result [$j] ^= $result [$j + 1];
      }
      $root = padQrMultiply ( $root, 0x02 );
    }

    return $result;

  }

  function padQrRemainder ( $data, $divisor ) {

    $result = array_fill ( 0, count ( $divisor ), 0 );

    foreach ( $data as $byte ) {
      $factor = $byte ^ array_shift ( $result );
      $result [] = 0;
      foreach ( $divisor as $i => $coefficient )
        $result [$i] ^= padQrMultiply ( $coefficient, $factor );
    }

    return $result;

  }

  // The finders, timing, alignment, version information and a reserved format area.

  function padQrFunctionPatterns ( &$m, &$f, $version, $level ) {

    $size = count ( $m );
    $set  = function ( $x, $y, $dark ) use ( &$m, &$f ) { $m [$y] [$x] = $dark; $f [$y] [$x] = TRUE; };

    for ( $i = 0; $i < $size; $i++ ) {
      $set ( 6, $i, $i % 2 == 0 );
      $set ( $i, 6, $i % 2 == 0 );
    }

    foreach ( [ [ 3, 3 ], [ $size - 4, 3 ], [ 3, $size - 4 ] ] as list ( $cx, $cy ) )
      for ( $dy = -4; $dy <= 4; $dy++ )
        for ( $dx = -4; $dx <= 4; $dx++ ) {
          $x = $cx + $dx;
          $y = $cy + $dy;
          if ( $x >= 0 and $x < $size and $y >= 0 and $y < $size ) {
            $dist = max ( abs ( $dx ), abs ( $dy ) );
            $set ( $x, $y, $dist != 2 and $dist != 4 );
          }
        }

    $positions = padQrAlignment ( $version );
    $last      = count ( $positions ) - 1;

    foreach ( $positions as $i => $cx )
      foreach ( $positions as $j => $cy )
        if ( ! ( ( $i == 0 and $j == 0 ) or ( $i == 0 and $j == $last ) or ( $i == $last and $j == 0 ) ) )
          for ( $dy = -2; $dy <= 2; $dy++ )
            for ( $dx = -2; $dx <= 2; $dx++ )
              $set ( $cx + $dx, $cy + $dy, max ( abs ( $dx ), abs ( $dy ) ) != 1 );

    padQrFormat ( $m, $f, $level, 0 );

    if ( $version >= 7 ) {
      $rem = $version;
      for ( $i = 0; $i < 12; $i++ )
        $rem = ( $rem << 1 ) ^ ( ( $rem >> 11 ) * 0x1F25 );
      $bits = $version << 12 | $rem;
      for ( $i = 0; $i < 18; $i++ ) {
        $dark = ( ( $bits >> $i ) & 1 ) == 1;
        $a    = $size - 11 + $i % 3;
        $b    = intdiv ( $i, 3 );
        $set ( $a, $b, $dark );
        $set ( $b, $a, $dark );
      }
    }

  }

  function padQrAlignment ( $version ) {

    if ( $version == 1 )
      return [];

    $align  = intdiv ( $version, 7 ) + 2;
    $step   = intdiv ( $version * 8 + $align * 3 + 5, $align * 4 - 4 ) * 2;
    $result = [ 6 ];

    for ( $pos = $version * 4 + 10; count ( $result ) < $align; $pos -= $step )
      array_splice ( $result, 1, 0, [ $pos ] );

    return $result;

  }

  // The 15 format bits - the level and the mask with their BCH code - in both copies.

  function padQrFormat ( &$m, &$f, $level, $mask ) {

    $size = count ( $m );
    $data = [ 'L' => 1, 'M' => 0, 'Q' => 3, 'H' => 2 ] [$level] << 3 | $mask;
    $rem  = $data;

    for ( $i = 0; $i < 10; $i++ )
      $rem = ( $rem << 1 ) ^ ( ( $rem >> 9 ) * 0x537 );

    $bits = ( $data << 10 | $rem ) ^ 0x5412;
    $bit  = fn ( $i ) => ( ( $bits >> $i ) & 1 ) == 1;
    $set  = function ( $x, $y, $dark ) use ( &$m, &$f ) { $m [$y] [$x] = $dark; $f [$y] [$x] = TRUE; };

    for ( $i = 0; $i <= 5; $i++ )
      $set ( 8, $i, $bit ( $i ) );

    $set ( 8, 7, $bit ( 6 ) );
    $set ( 8, 8, $bit ( 7 ) );
    $set ( 7, 8, $bit ( 8 ) );

    for ( $i = 9; $i < 15; $i++ )
      $set ( 14 - $i, 8, $bit ( $i ) );

    for ( $i = 0; $i < 8; $i++ )
      $set ( $size - 1 - $i, 8, $bit ( $i ) );

    for ( $i = 8; $i < 15; $i++ )
      $set ( 8, $size - 15 + $i, $bit ( $i ) );

    $set ( 8, $size - 8, TRUE );

  }

  // The codewords in the zigzag: two columns at a time from the right, up and down in turn,
  // past the timing column, around every function module.

  function padQrPlace ( &$m, $f, $codewords ) {

    $size  = count ( $m );
    $total = count ( $codewords ) * 8;
    $i     = 0;

    for ( $right = $size - 1; $right >= 1; $right -= 2 ) {

      if ( $right == 6 )
        $right = 5;

      for ( $vert = 0; $vert < $size; $vert++ )
        for ( $j = 0; $j < 2; $j++ ) {
          $x  = $right - $j;
          $up = ( ( $right + 1 ) & 2 ) == 0;
          $y  = $up ? $size - 1 - $vert : $vert;
          if ( ! $f [$y] [$x] and $i < $total ) {
            $m [$y] [$x] = ( ( $codewords [$i >> 3] >> ( 7 - ( $i & 7 ) ) ) & 1 ) == 1;
            $i++;
          }
        }

    }

  }

  // A mask turns the data modules over where its condition holds; twice is undone.

  function padQrMask ( &$m, $f, $mask ) {

    $size = count ( $m );

    for ( $y = 0; $y < $size; $y++ )
      for ( $x = 0; $x < $size; $x++ ) {

        if ( $f [$y] [$x] )
          continue;

        switch ( $mask ) {
          case 0: $flip = ( $x + $y ) % 2 == 0; break;
          case 1: $flip = $y % 2 == 0; break;
          case 2: $flip = $x % 3 == 0; break;
          case 3: $flip = ( $x + $y ) % 3 == 0; break;
          case 4: $flip = ( intdiv ( $x, 3 ) + intdiv ( $y, 2 ) ) % 2 == 0; break;
          case 5: $flip = $x * $y % 2 + $x * $y % 3 == 0; break;
          case 6: $flip = ( $x * $y % 2 + $x * $y % 3 ) % 2 == 0; break;
          default: $flip = ( ( $x + $y ) % 2 + $x * $y % 3 ) % 2 == 0;
        }

        if ( $flip )
          $m [$y] [$x] = ! $m [$y] [$x];

      }

  }

  // The four penalty rules: runs of five or more, 2x2 blocks, finder-like patterns, and
  // the share of dark modules away from half.

  function padQrPenalty ( $m ) {

    $size    = count ( $m );
    $penalty = 0;
    $dark    = 0;
    $lines   = [];

    for ( $y = 0; $y < $size; $y++ ) {
      $row = $col = '';
      for ( $x = 0; $x < $size; $x++ ) {
        $row .= $m [$y] [$x] ? '1' : '0';
        $col .= $m [$x] [$y] ? '1' : '0';
      }
      $lines [] = $row;
      $lines [] = $col;
      $dark    += substr_count ( $row, '1' );
    }

    foreach ( $lines as $line ) {
      preg_match_all ( '/0{5,}|1{5,}/', $line, $runs );
      foreach ( $runs [0] as $run )
        $penalty += 3 + strlen ( $run ) - 5;
      $penalty += 40 * ( preg_match_all ( '/(?=10111010000|00001011101)/', $line ) );
    }

    for ( $y = 0; $y < $size - 1; $y++ )
      for ( $x = 0; $x < $size - 1; $x++ )
        if ( $m [$y] [$x] == $m [$y] [$x + 1] and $m [$y] [$x] == $m [$y + 1] [$x] and $m [$y] [$x] == $m [$y + 1] [$x + 1] )
          $penalty += 3;

    $total    = $size * $size;
    $penalty += 10 * max ( 0, (int) ceil ( abs ( $dark * 20 - $total * 10 ) / $total ) - 1 );

    return $penalty;

  }

?>
