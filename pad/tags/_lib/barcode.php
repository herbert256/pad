<?php

  // Barcodes - the {barcode} tag: EAN-13, EAN-8, UPC-A and Code 128, written as inline SVG.
  //
  //   {barcode '871234567890'}                      EAN-13, the check digit added
  //   {barcode 'PAD-2026-0042', type='code128'}     any printable ASCII
  //   {barcode '03600029145', type='upca', height=50, plain}
  //
  // padBarcode         the SVG, or '' with the reason in $error
  // padBarcodeModules  the bars as a string of 1 (bar) and 0 (space), one per module, and
  //                    the digits or text written under them
  //
  // An EAN or UPC number is given with or without its check digit: without it, it is
  // added; with it, it is checked. Code 128 switches to its double-density digit set C for
  // a run of four or more digits, and to set B for the rest.

  const PAD_EAN_L = [ '0001101', '0011001', '0010011', '0111101', '0100011', '0110001', '0101111', '0111011', '0110111', '0001011' ];
  const PAD_EAN_G = [ '0100111', '0110011', '0011011', '0100001', '0011101', '0111001', '0000101', '0010001', '0001001', '0010111' ];
  const PAD_EAN_R = [ '1110010', '1100110', '1101100', '1000010', '1011100', '1001110', '1010000', '1000100', '1001000', '1110100' ];

  const PAD_EAN_PARITY = [ 'LLLLLL', 'LLGLGG', 'LLGGLG', 'LLGGGL', 'LGLLGG', 'LGGLLG', 'LGGGLL', 'LGLGLG', 'LGLGGL', 'LGGLGL' ];

  const PAD_CODE128 = [
    '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
    '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132',
    '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
    '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
    '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
    '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
    '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
    '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111',
    '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141',
    '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
    '114131', '311141', '411131', '211412', '211214', '211232', '2331112' ];

  // The SVG. $scale is the width of one module in pixels, $height that of the bars; the
  // digits or the text go under them unless $plain. An EAN or UPC has its guard bars drawn
  // longer, its digits set in the groups they belong to.

  function padBarcode ( $text, $type, $height, $scale, $title, $plain, $dark, $light, &$error ) {

    $coded = padBarcodeModules ( (string) $text, $type, $error );

    if ( ! $coded )
      return '';

    list ( $bars, $groups, $guards ) = $coded;

    $quiet = ( $type == 'code128' ) ? 10 : ( $type == 'ean8' ? 7 : 11 );
    $width = ( strlen ( $bars ) + 2 * $quiet ) * $scale;
    $font  = round ( 5.5 * $scale + 1, 1 );
    $total = $height + ( $plain ? 0 : round ( $font * 1.2 ) );
    $long  = $plain ? $height : $height + $font / 2;
    $path  = '';

    for ( $x = 0; $x < strlen ( $bars ); $x++ )
      if ( $bars [$x] == '1' ) {
        $run   = strspn ( $bars, '1', $x );
        $end   = ( $guards and padBarcodeGuard ( $x, $type ) ) ? $long : $height;
        $path .= 'M' . padBarcodeNum ( ( $x + $quiet ) * $scale ) . ',0h' . padBarcodeNum ( $run * $scale )
               . 'v' . padBarcodeNum ( $end ) . 'h-' . padBarcodeNum ( $run * $scale ) . 'z';
        $x    += $run - 1;
      }

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="pad-barcode" role="img"'
         . ' width="' . padBarcodeNum ( $width ) . '" height="' . padBarcodeNum ( $total ) . '"'
         . ' viewBox="0 0 ' . padBarcodeNum ( $width ) . ' ' . padBarcodeNum ( $total ) . '" shape-rendering="crispEdges">'
         . '<title>' . padQrAttr ( $title !== '' ? $title : implode ( '', array_column ( $groups, 1 ) ) ) . '</title>'
         . '<rect width="100%" height="100%" fill="' . padQrAttr ( $light ) . '"/>'
         . '<path fill="' . padQrAttr ( $dark ) . "\" d=\"$path\"/>";

    if ( ! $plain )
      foreach ( $groups as list ( $center, $digits ) )
        $svg .= '<text x="' . padBarcodeNum ( ( $center + $quiet ) * $scale ) . '" y="' . padBarcodeNum ( $total - $font * 0.2 ) . '"'
              . ' text-anchor="middle" font-family="ui-monospace,Menlo,Consolas,monospace" font-size="' . padBarcodeNum ( $font ) . '"'
              . ' fill="' . padQrAttr ( $dark ) . '"'
              . ( $guards ? ' textLength="' . padBarcodeNum ( strlen ( $digits ) * 6 * $scale ) . '" lengthAdjust="spacing"' : '' ) . '>'
              . padQrAttr ( $digits ) . '</text>';

    return "$svg</svg>";

  }

  function padBarcodeNum ( $n ) {

    return rtrim ( rtrim ( number_format ( $n, 2, '.', '' ), '0' ), '.' );

  }

  // Whether the module at $x belongs to a guard: the start, the middle and the end.

  function padBarcodeGuard ( $x, $type ) {

    $half = ( $type == 'ean8' ) ? 4 : 6;
    $mid  = 3 + $half * 7;

    return $x < 3 or ( $x >= $mid and $x < $mid + 5 ) or $x >= $mid + 5 + $half * 7;

  }

  // [ bars, groups of the text under them as [ centre, text ], guards drawn longer ].

  function padBarcodeModules ( $text, $type, &$error ) {

    $error = '';

    if ( $type == 'code128' )
      return padBarcode128 ( $text, $error );

    $digits = [ 'ean13' => 13, 'ean8' => 8, 'upca' => 12 ] [$type] ?? 0;

    if ( ! $digits ) {
      $error = "there is no barcode type '" . padMakeSafe ( $type, 20 ) . "' - ean13, ean8, upca or code128";
      return NULL;
    }

    if ( ! ctype_digit ( $text ) or ( strlen ( $text ) != $digits and strlen ( $text ) != $digits - 1 ) ) {
      $error = "an $type barcode is $digits digits, or " . ( $digits - 1 ) . ' without the check digit';
      return NULL;
    }

    $check = padBarcodeCheck ( substr ( $text, 0, $digits - 1 ) );

    if ( strlen ( $text ) == $digits and $text [$digits - 1] != $check ) {
      $error = "the check digit of $text is $check, not " . $text [$digits - 1];
      return NULL;
    }

    $code = substr ( $text, 0, $digits - 1 ) . $check;

    if ( $type == 'ean8' ) {
      $bars = '101';
      for ( $i = 0; $i < 4; $i++ ) $bars .= PAD_EAN_L [ $code [$i] ];
      $bars .= '01010';
      for ( $i = 4; $i < 8; $i++ ) $bars .= PAD_EAN_R [ $code [$i] ];
      $bars .= '101';
      return [ $bars, [ [ 3 + 14, substr ( $code, 0, 4 ) ], [ 36 + 14, substr ( $code, 4 ) ] ], TRUE ];
    }

    // UPC-A is an EAN-13 that starts with 0: the same bars, its own grouping of the digits.

    $ean    = ( $type == 'upca' ) ? "0$code" : $code;
    $parity = PAD_EAN_PARITY [ $ean [0] ];
    $bars   = '101';

    for ( $i = 1; $i <= 6; $i++ )
      $bars .= ( $parity [$i - 1] == 'L' ) ? PAD_EAN_L [ $ean [$i] ] : PAD_EAN_G [ $ean [$i] ];

    $bars .= '01010';

    for ( $i = 7; $i <= 12; $i++ )
      $bars .= PAD_EAN_R [ $ean [$i] ];

    $bars .= '101';

    if ( $type == 'upca' )
      $groups = [ [ -5, $code [0] ], [ 3 + 7 + 17.5, substr ( $code, 1, 5 ) ], [ 50 + 17.5, substr ( $code, 6, 5 ) ], [ 95 + 5, $code [11] ] ];
    else
      $groups = [ [ -5, $ean [0] ], [ 3 + 21, substr ( $ean, 1, 6 ) ], [ 50 + 21, substr ( $ean, 7 ) ] ];

    return [ $bars, $groups, TRUE ];

  }

  // The check digit of the digits before it: weights 3 and 1 from the right, 3 first.

  function padBarcodeCheck ( $digits ) {

    $sum = 0;

    foreach ( array_reverse ( str_split ( $digits ) ) as $i => $digit )
      $sum += $digit * ( $i % 2 == 0 ? 3 : 1 );

    return ( 10 - $sum % 10 ) % 10;

  }

  // Code 128: start B, or C when the text opens with four digits or more; inside, a run of
  // digits that saves a symbol goes in C - an even number of them, the odd one in B.

  function padBarcode128 ( $text, &$error ) {

    if ( $text === '' or preg_match ( '/[^\x20-\x7E]/', $text ) ) {
      $error = 'a code128 barcode takes printable ASCII - letters, digits, space and punctuation';
      return NULL;
    }

    $digits = fn ( $at ) => strspn ( $text, '0123456789', $at );
    $length = strlen ( $text );
    $values = [];
    $set    = '';
    $i      = 0;

    while ( $i < $length ) {

      $run    = $digits ( $i );
      $useC   = ( $run >= 4 && ( $i + $run == $length || $i == 0 || $run >= 6 ) ) || ( $set == 'C' && $run >= 2 );

      if ( $useC ) {
        $take = $run - $run % 2;
        if ( $set != 'C' )
          $values [] = $set == '' ? 105 : 99;
        $set = 'C';
        for ( $k = 0; $k < $take; $k += 2 )
          $values [] = (int) substr ( $text, $i + $k, 2 );
        $i += $take;
        continue;
      }

      if ( $set != 'B' )
        $values [] = $set == '' ? 104 : 100;

      $set       = 'B';
      $values [] = ord ( $text [$i] ) - 32;
      $i++;

    }

    $sum = $values [0];

    foreach ( $values as $k => $value )
      if ( $k )
        $sum += $k * $value;

    $values [] = $sum % 103;
    $values [] = 106;

    $bars = '';

    foreach ( $values as $value )
      foreach ( str_split ( PAD_CODE128 [$value] ) as $k => $w )
        $bars .= str_repeat ( $k % 2 ? '0' : '1', (int) $w );

    return [ $bars, [ [ strlen ( $bars ) / 2, $text ] ], FALSE ];

  }

?>
