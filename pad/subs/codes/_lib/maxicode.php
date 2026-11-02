<?php
  // Native bounded MaxiCode encoder. Table/layout/primary fields adapted from Zint;
  // see SPECIALIZED-CODES-LICENSE. Secondary data uses Code A plus one-character
  // shifts, and nine-digit compaction. This deliberately does not optimize latches.
  include_once PAD . 'subs/codes/_lib/qr.php';

  function padMaxicodeEncode ( $text, $options = [] ) {
    if ( ! is_string ( $text ) || $text === '' || strlen ( $text ) > 4096 || ! is_array ( $options ) ) return 'MaxiCode requires nonempty text of at most 4096 bytes and an options array';
    foreach ( $options as $key => $value ) if ( ! in_array ( $key, [ 'mode', 'encoding', 'postcode', 'country', 'service' ], TRUE ) ) return 'unknown MaxiCode option';
    $mode = $options ['mode'] ?? 4; $encoding = $options ['encoding'] ?? 'ISO-8859-1';
    if ( ! is_int ( $mode ) || ! in_array ( $mode, [ 2, 3, 4, 5 ], TRUE ) || ! in_array ( $encoding, [ 'ISO-8859-1', 'UTF-8', 'binary' ], TRUE ) ) return 'MaxiCode mode must be 2, 3, 4 or 5; encoding must be ISO-8859-1, UTF-8 or binary';
    if ( $encoding !== 'binary' && ! mb_check_encoding ( $text, 'UTF-8' ) ) return 'MaxiCode text must be valid UTF-8';
    $bytes = $text;
    if ( $encoding === 'ISO-8859-1' ) { $bytes = mb_convert_encoding ( $text, 'ISO-8859-1', 'UTF-8' ); if ( mb_convert_encoding ( $bytes, 'UTF-8', 'ISO-8859-1' ) !== $text ) return 'MaxiCode text cannot be represented in ISO-8859-1'; }
    static $table; $table ??= include PAD . 'subs/codes/_lib/maxicode-tables.php';
    $words = array_fill ( 0, 144, 0 ); $words [0] = $mode;
    if ( $mode <= 3 ) {
      $postcode = $options ['postcode'] ?? NULL; $country = $options ['country'] ?? NULL; $service = $options ['service'] ?? NULL;
      if ( ! is_string ( $postcode ) || $postcode === '' || ! is_int ( $country ) || $country < 0 || $country > 999 || ! is_int ( $service ) || $service < 0 || $service > 999 ) return 'structured MaxiCode requires a postcode string and integer country/service in 0..999';
      if ( $mode === 2 ) {
        if ( ! ctype_digit ( $postcode ) || strlen ( $postcode ) > 9 ) return 'MaxiCode mode 2 postcode requires 1..9 digits';
        if ( $country === 840 && strlen ( $postcode ) === 5 ) $postcode .= '0000';
        $length = strlen ( $postcode ); $number = (int) $postcode;
        $words [0] = ( ( $number & 3 ) << 4 ) | 2; $words [1] = ( $number >> 2 ) & 63; $words [2] = ( $number >> 8 ) & 63;
        $words [3] = ( $number >> 14 ) & 63; $words [4] = ( $number >> 20 ) & 63; $words [5] = ( ( $number >> 26 ) & 15 ) | ( ( $length & 3 ) << 4 );
        $words [6] = ( ( $length >> 2 ) & 15 ) | ( ( $country & 3 ) << 4 );
      } else {
        if ( strlen ( $postcode ) > 6 ) return 'MaxiCode mode 3 postcode requires at most six Code A characters';
        $postcode = str_pad ( $postcode, 6, ' ' ); $codes = [];
        foreach ( str_split ( $postcode ) as $character ) { $byte = ord ( $character ); if ( $byte < 32 || ! ( $table ['sets'] [$byte] & 1 ) ) return 'MaxiCode mode 3 postcode accepts uppercase Code A characters only'; $codes [] = $table ['symbols'] [$byte]; }
        $words [0] = ( ( $codes [5] & 3 ) << 4 ) | 3;
        for ( $i = 1; $i <= 5; $i++ ) $words [$i] = ( ( $codes [5 - $i] & 3 ) << 4 ) | ( ( $codes [6 - $i] & 60 ) >> 2 );
        $words [6] = ( ( $codes [0] & 60 ) >> 2 ) | ( ( $country & 3 ) << 4 );
      }
      $words [7] = ( $country >> 2 ) & 63; $words [8] = ( ( $country >> 8 ) & 3 ) | ( ( $service & 15 ) << 2 ); $words [9] = ( $service >> 4 ) & 63;
    } elseif ( array_intersect ( array_keys ( $options ), [ 'postcode', 'country', 'service' ] ) ) return 'primary address fields apply only to MaxiCode modes 2 and 3';
    $payload = $encoding === 'UTF-8' ? [ 27, 26 ] : [];
    for ( $i = 0; $i < strlen ( $bytes ); $i++ ) {
      if ( $i + 9 <= strlen ( $bytes ) && ctype_digit ( substr ( $bytes, $i, 9 ) ) ) {
        $number = (int) substr ( $bytes, $i, 9 ); $payload [] = 31;
        foreach ( [ 24, 18, 12, 6, 0 ] as $shift ) $payload [] = ( $number >> $shift ) & 63;
        $i += 8; continue;
      }
      $byte = ord ( $bytes [$i] ); $sets = $table ['sets'] [$byte];
      if ( $sets & 1 ) $payload [] = $table ['symbols'] [$byte];
      else {
        // Characters shared with A were handled above, so the table's canonical
        // value is correct for every shifted-only byte.
        $payload [] = $sets & 2 ? 59 : ( $sets & 4 ? 62 : ( $sets & 8 ? 60 : 61 ) );
        $payload [] = $table ['symbols'] [$byte];
      }
    }
    $capacity = $mode <= 3 ? 84 : ( $mode === 5 ? 77 : 93 );
    if ( count ( $payload ) > $capacity ) return 'MaxiCode payload exceeds the supported single-shift encoding capacity';
    $used = count ( $payload ); $payload = array_pad ( $payload, $capacity, 33 );
    if ( $mode > 3 ) { for ( $i = 0; $i < 9; $i++ ) $words [$i + 1] = $payload [$i]; $payload = array_slice ( $payload, 9 ); }
    foreach ( $payload as $i => $word ) $words [$i + 20] = $word;
    foreach ( padMaxicodeEcc ( array_slice ( $words, 0, 10 ), 10 ) as $i => $word ) $words [$i + 10] = $word;
    $secondary = $mode === 5 ? 68 : 84; $errorLength = $mode === 5 ? 28 : 20;
    for ( $parity = 0; $parity < 2; $parity++ ) { $data = []; for ( $i = $parity; $i < $secondary; $i += 2 ) $data [] = $words [$i + 20];
      foreach ( padMaxicodeEcc ( $data, $errorLength ) as $i => $word ) $words [$secondary + 20 + 2 * $i + $parity] = $word;
    }
    $matrix = array_fill ( 0, 33, array_fill ( 0, 30, 0 ) );
    foreach ( $table ['grid'] as $i => $sequence ) if ( $sequence ) { $at = $sequence + 5; $matrix [intdiv ( $i, 30 )] [$i % 30] = ( $words [intdiv ( $at, 6 ) - 1] >> ( 5 - $at % 6 ) ) & 1; }
    foreach ( [ [0,28],[0,29],[9,10],[9,11],[10,11],[15,7],[16,8],[16,20],[17,20],[22,10],[23,10],[22,17],[23,17] ] as [ $y, $x ] ) $matrix [$y] [$x] = 1;
    return [ 'matrix' => $matrix, 'codewords' => $words, 'mode' => $mode, 'encoding' => $encoding, 'usedCodewords' => $used, 'capacity' => $capacity ];
  }

  function padMaxicodeMultiply ( $x, $y ) {
    $result = 0;
    for ( $i = 5; $i >= 0; $i-- ) { $result = ( $result << 1 ) ^ ( ( $result >> 5 ) * 0x43 ); $result ^= ( ( $y >> $i ) & 1 ) * $x; }
    return $result & 63;
  }

  function padMaxicodeEcc ( $data, $degree ) {
    $polynomial = array_fill ( 0, $degree, 0 ); $polynomial [$degree - 1] = 1; $root = 2;
    for ( $i = 0; $i < $degree; $i++ ) { for ( $j = 0; $j < $degree; $j++ ) { $polynomial [$j] = padMaxicodeMultiply ( $polynomial [$j], $root ); if ( $j + 1 < $degree ) $polynomial [$j] ^= $polynomial [$j + 1]; } $root = padMaxicodeMultiply ( $root, 2 ); }
    $result = array_fill ( 0, $degree, 0 );
    foreach ( $data as $word ) { $factor = $word ^ array_shift ( $result ); $result [] = 0; foreach ( $polynomial as $i => $coefficient ) $result [$i] ^= padMaxicodeMultiply ( $coefficient, $factor ); }
    return $result;
  }

  function padMaxicodeSvg ( $code, $size = 240, $title = '', $dark = '#000', $light = '#fff' ) {
    $quiet = 1.0; $radius = 1 / sqrt ( 3 ); $rowStep = sqrt ( 3 ) / 2; $width = 30 + 2 * $quiet; $height = 32 * $rowStep + 2 * $radius + 2 * $quiet;
    $number = fn ( $n ) => rtrim ( rtrim ( number_format ( $n, 6, '.', '' ), '0' ), '.' );
    $path = '';
    foreach ( $code ['matrix'] as $row => $values ) for ( $column = 0; $column < 30 - $row % 2; $column++ ) if ( $values [$column] ) {
      $x = $column + ( $row % 2 ? 1 : .5 ) + $quiet; $y = $row * $rowStep + $radius + $quiet;
      $path .= 'M' . $number ( $x ) . ',' . $number ( $y - $radius );
      foreach ( [ [ .5, -$radius / 2 ], [ .5, $radius / 2 ], [ 0, $radius ], [ -.5, $radius / 2 ], [ -.5, -$radius / 2 ] ] as [ $dx, $dy ] ) $path .= 'L' . $number ( $x + $dx ) . ',' . $number ( $y + $dy );
      $path .= 'z';
    }
    $size = max ( 64, min ( 4096, (int) $size ) ); $pixelHeight = (int) round ( $size * $height / $width );
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="pad-maxicode" role="img" width="' . $size . '" height="' . $pixelHeight . '" viewBox="0 0 ' . $number ( $width ) . ' ' . $number ( $height ) . '">'
      . '<title>' . padQrAttr ( $title ) . '</title><rect width="100%" height="100%" fill="' . padQrAttr ( $light ) . '"/>'
      . '<path fill="' . padQrAttr ( $dark ) . '" d="' . $path . '"/>';
    $increment = ( 9 - 2 * $radius ) / 5; $stroke = $increment / 2;
    foreach ( [ 5, 3, 1 ] as $ring ) $svg .= '<circle cx="' . $number ( 14.5 + $quiet ) . '" cy="' . $number ( $height / 2 ) . '" r="' . $number ( ( 2 * $radius + $increment * $ring - $stroke ) / 2 ) . '" fill="none" stroke="' . padQrAttr ( $dark ) . '" stroke-width="' . $number ( $stroke ) . '"/>';
    return $svg . '</svg>';
  }
?>
