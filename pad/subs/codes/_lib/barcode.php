<?php

  // The codes subsystem: barcodes and two-dimensional codes, encoded on the server and written
  // as inline SVG - no image library, no font, no remote service - so a code prints, caches and
  // goes in an e-mail like any other output.
  //
  //   {barcode '871234567890'/}                      EAN-13, the check digit added
  //   {barcode 'PAD-2026-0042', type='code128'/}     any printable ASCII
  //   {barcode:ean8 '9638507'/}                      the barcode: type
  //   {code128 'PAD-2026-0042'/}                     bare, when nothing else has the name
  //   {qr 'https://example.com'/}                    the QR code, a symbology with a tag of its own
  //
  // A symbology is a file of subs/codes/types/: the files there are the barcode types there are
  // (padKinds ('codes'), lib/subsystem.php), and subs/codes/barcode.php is the body the {barcode}
  // tag, the {qr} tag and the barcode: type all run. A symbology's file encodes the text and
  // answers the SVG, drawn by the functions of this file; the encoder of a symbology that is
  // not one of the first four stands in a file of this directory of its own, included by the
  // symbology's file when a page draws one.
  //
  //   padCodeTag         runs the file of a symbology, the text it encodes at hand
  //   padCodeText        the text: the first parameter as the characters it holds
  //   padCodeLinear      the SVG of a one-dimensional code, its options read - height, scale,
  //                      title, plain, color, background - or '' with the reason raised
  //   padCodeMatrix      the SVG of a two-dimensional code from its rows of dark and light
  //   padCodeFail        the reason a code cannot be drawn, raised under the strict check
  //   padCodeElements    the modules of a symbol of narrow and wide elements
  //   padBarcode         the SVG of the bars: a string of 1 (bar) and 0 (space), one per
  //                      module, the text written under them, the guard bars drawn longer
  //   padBarcodeModules  EAN-13, EAN-8, UPC-A and Code 128: the bars and the text under them
  //
  // An EAN or UPC number is given with or without its check digit: without it, it is
  // added; with it, it is checked. Code 128 switches to its double-density digit set C for
  // a run of four or more digits, and to set B for the rest. The text is escaped as {qr}
  // escapes it, padQrAttr of subs/codes/_lib/qr.php.

  include_once PAD . 'subs/codes/_lib/qr.php';

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

  // The file of the symbology, run with the text at hand: what it answers is the code - the
  // SVG, or '' when the text cannot be encoded, the reason raised. A code drawn while another
  // one is - by a tag in the title of an application's own - gives the text back after.

  function padCodeTag ( $kind, $parm ) {

    global $padCodeNow;

    $before     = $padCodeNow ?? NULL;
    $padCodeNow = [ 'kind' => $kind, 'parm' => $parm ];

    $svg = include PAD . "subs/codes/types/$kind.php";

    $padCodeNow = $before;

    return $svg;

  }

  // The text of the code as the characters it holds (padParmText), a field inside it neither
  // stand-ins nor escaped - a , of SKU-{$sku} came as U+E02C, which no barcode encodes - and
  // for the bars without the white space around it.

  function padCodeText ( $trim = TRUE ) {

    $parm = $GLOBALS ['padCodeNow'] ['parm'] ?? '';

    if ( is_array ( $parm ) )
      return '';

    return $trim ? trim ( padParmText ( $parm ) ) : padParmText ( $parm );

  }

  // A code that cannot be drawn: the reason raised under the strict check, nothing written.

  function padCodeFail ( $message ) {

    if ( $GLOBALS ['padCheckSyntax'] ?? FALSE )
      padError ( $message );

    return '';

  }

  // A bare option of a symbology - check - as plain is one of every barcode.

  function padCodeFlag ( $option ) {

    return (bool) padTagParm ( $option, FALSE );

  }

  // The names of the symbologies, for what a message lists: a, b or c.

  function padCodeList () {

    $kinds = padKinds ( 'codes' );
    $last  = array_pop ( $kinds );

    return $kinds ? implode ( ', ', $kinds ) . " or $last" : (string) $last;

  }

  // A one-dimensional code: its options read first - so an option given is read whether the
  // text encodes or not - then the bars the encoder made, or the reason it could not.
  // $coded is what an encoder answers: [ bars, groups, guards, quiet zone, bearer ], or the
  // message saying why the text is no code of the kind.

  function padCodeLinear ( $coded ) {

    $height = max ( 10, (int) padTagParm ( 'height', 60 ) );
    $scale  = max ( 1, min ( 10, (float) padTagParm ( 'scale', 2 ) ) );
    $title  = padParmText ( padTagParm ( 'title' ) );
    $plain  = (bool) padTagParm ( 'plain', FALSE );
    $dark   = (string) padTagParm ( 'color', '#000' );
    $light  = (string) padTagParm ( 'background', '#fff' );

    if ( ! is_array ( $coded ) )
      return padCodeFail ( (string) $coded );

    return padBarcode ( $coded, $height, $scale, $title, $plain, $dark, $light );

  }

  // The modules of a symbol written as narrow and wide elements - Code 39, Codabar: $count
  // bits, the first element first, a bar, then a space, and so on; a 1 is a wide element of
  // $wide modules, a 0 a narrow one of one.

  function padCodeElements ( $bits, $count, $wide ) {

    $modules = '';

    for ( $k = 0; $k < $count; $k++ )
      $modules .= str_repeat ( $k % 2 ? '0' : '1', ( $bits >> ( $count - 1 - $k ) & 1 ) ? $wide : 1 );

    return $modules;

  }

  // The SVG of the bars. $coded is [ bars, groups, guards, quiet zone, bearer ]: the bars a
  // string of 1 and 0, a module each; the groups of the text under them as [ centre, text ],
  // the centre counted in modules from the first bar; the guards the ranges of modules
  // [ from, to ) drawn longer, empty for none; the quiet zone the modules of space on either
  // side; the bearer the modules of a bar above and below the code, as wide as it with its
  // quiet zones - an ITF-14's - or 0. $scale is the width of one module in pixels, $height
  // that of the bars; the digits or the text go under them unless $plain. An EAN or UPC has
  // its digits set in the groups they belong to, stretched to them.

  function padBarcode ( $coded, $height, $scale, $title, $plain, $dark, $light ) {

    list ( $bars, $groups, $guards, $quiet ) = $coded;

    $bearer = ( $coded [4] ?? 0 ) * $scale;
    $width  = ( strlen ( $bars ) + 2 * $quiet ) * $scale;
    $font   = round ( 5.5 * $scale + 1, 1 );
    $total  = $height + 2 * $bearer + ( $plain ? 0 : round ( $font * 1.2 ) );
    $long   = $plain ? $height : $height + $font / 2;
    $top    = $bearer ? padBarcodeNum ( $bearer ) : '0';
    $path   = '';

    for ( $x = 0; $x < strlen ( $bars ); $x++ )
      if ( $bars [$x] == '1' ) {
        $run   = strspn ( $bars, '1', $x );
        $end   = ( $guards and padBarcodeGuard ( $x, $guards ) ) ? $long : $height;
        $path .= 'M' . padBarcodeNum ( ( $x + $quiet ) * $scale ) . ",{$top}h" . padBarcodeNum ( $run * $scale )
               . 'v' . padBarcodeNum ( $end ) . 'h-' . padBarcodeNum ( $run * $scale ) . 'z';
        $x    += $run - 1;
      }

    if ( $bearer )
      foreach ( [ 0, $bearer + $height ] as $y )
        $path .= 'M0,' . padBarcodeNum ( $y ) . 'h' . padBarcodeNum ( $width ) . 'v' . padBarcodeNum ( $bearer )
               . 'h-' . padBarcodeNum ( $width ) . 'z';

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

  // A two-dimensional code: the rows of TRUE (dark) and FALSE (light) modules, no quiet zone,
  // drawn in a light rectangle $size pixels wide with a quiet zone of $quiet modules around
  // them, the dark modules as one path of horizontal runs. The colours are fixed dark on
  // light by default - a reader needs the contrast, whatever the page's colour scheme.

  function padCodeMatrix ( $matrix, $quiet, $size, $title, $dark, $light, $class ) {

    $rows = count ( $matrix );
    $cols = count ( $matrix [0] );
    $wide = $cols + 2 * $quiet;
    $high = $rows + 2 * $quiet;
    $path = '';

    foreach ( $matrix as $y => $row )
      for ( $x = 0; $x < $cols; $x++ )
        if ( $row [$x] ) {
          $run = 1;
          while ( $x + $run < $cols and $row [$x + $run] )
            $run++;
          $path .= 'M' . ( $x + $quiet ) . ',' . ( $y + $quiet ) . "h{$run}v1h-{$run}z";
          $x += $run;
        }

    $width  = (int) $size;
    $height = ( $high == $wide ) ? $width : (int) round ( $size * $high / $wide );

    return "<svg xmlns=\"http://www.w3.org/2000/svg\" class=\"$class\" role=\"img\" width=\"$width\" height=\"$height\""
         . " viewBox=\"0 0 $wide $high\" shape-rendering=\"crispEdges\">"
         . '<title>' . padQrAttr ( $title ) . '</title>'
         . '<rect width="100%" height="100%" fill="' . padQrAttr ( $light ) . '"/>'
         . '<path fill="' . padQrAttr ( $dark ) . "\" d=\"$path\"/></svg>";

  }

  function padBarcodeNum ( $n ) {

    return rtrim ( rtrim ( number_format ( $n, 2, '.', '' ), '0' ), '.' );

  }

  // Whether the module at $x belongs to a guard drawn longer - for an EAN the start, the
  // middle and the end - given as the ranges [ from, to ) of modules they cover.

  function padBarcodeGuard ( $x, $guards ) {

    foreach ( $guards as list ( $from, $to ) )
      if ( $x >= $from and $x < $to )
        return TRUE;

    return FALSE;

  }

  // [ bars, groups of the text under them as [ centre, text ], guards drawn longer, quiet
  // zone ] of an EAN-13, EAN-8, UPC-A or Code 128, or the message why the text is none.

  function padBarcodeModules ( $text, $type ) {

    if ( $type == 'code128' )
      return padBarcode128 ( $text );

    $digits = [ 'ean13' => 13, 'ean8' => 8, 'upca' => 12 ] [$type];
    $code   = padBarcodeDigits ( $text, $type, $digits );

    if ( ! is_array ( $code ) )
      return $code;

    $code = $code [0];

    if ( $type == 'ean8' ) {
      $bars = '101';
      for ( $i = 0; $i < 4; $i++ ) $bars .= PAD_EAN_L [ $code [$i] ];
      $bars .= '01010';
      for ( $i = 4; $i < 8; $i++ ) $bars .= PAD_EAN_R [ $code [$i] ];
      $bars .= '101';
      return [ $bars, [ [ 3 + 14, substr ( $code, 0, 4 ) ], [ 36 + 14, substr ( $code, 4 ) ] ], [ [ 0, 3 ], [ 31, 36 ], [ 64, 67 ] ], 7 ];
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

    return [ $bars, $groups, [ [ 0, 3 ], [ 45, 50 ], [ 92, 95 ] ], 11 ];

  }

  // The digits of a code that ends in a check digit of weights 3 and 1 - an EAN, a UPC, an
  // ITF-14 - as [ the digits with the check digit ], or the message why the text is none: a
  // check digit left off is added, one given is checked.

  function padBarcodeDigits ( $text, $type, $digits ) {

    if ( ! ctype_digit ( $text ) or ( strlen ( $text ) != $digits and strlen ( $text ) != $digits - 1 ) )
      return "an $type barcode is $digits digits, or " . ( $digits - 1 ) . ' without the check digit';

    $check = padBarcodeCheck ( substr ( $text, 0, $digits - 1 ) );

    if ( strlen ( $text ) == $digits and $text [$digits - 1] != $check )
      return "the check digit of $text is $check, not " . $text [$digits - 1];

    return [ substr ( $text, 0, $digits - 1 ) . $check ];

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

  function padBarcode128 ( $text ) {

    if ( $text === '' or preg_match ( '/[^\x20-\x7E]/', $text ) )
      return 'a code128 barcode takes printable ASCII - letters, digits, space and punctuation';

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

    return [ $bars, [ [ strlen ( $bars ) / 2, $text ] ], [], 10 ];

  }

?>
