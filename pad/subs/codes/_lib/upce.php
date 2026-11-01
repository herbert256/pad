<?php

  // UPC-E, the UPC-A of a small package with its zeros left out: six digits stand for eleven,
  // and the check digit is that of the UPC-A they stand for. The number system, 0 or 1, and
  // the check digit are not drawn as bars of their own: they choose which of the six digits are
  // written with the odd and which with the even set of EAN bars (PAD_EAN_L and PAD_EAN_G of
  // subs/codes/_lib/barcode.php).
  //
  //   {upce '425261'/}         number system 0, the check digit added: 04252614
  //   {upce '04252614'/}       the same, the check digit checked
  //   {upce '04210000526'/}    a UPC-A number, written in its short form when it has one
  //
  // padCodeUpce        the bars, or the message why the text is no UPC-E
  // padCodeUpceExpand  the eleven digits of the UPC-A that six stand for

  // The sets of the six digits for number system 0, by the check digit: O the odd set (L),
  // E the even one (G). Number system 1 takes the other set throughout.

  const PAD_UPCE_PARITY = [ 'EEEOOO', 'EEOEOO', 'EEOOEO', 'EEOOOE', 'EOEEOO', 'EOOEEO', 'EOOOEE', 'EOEOEO', 'EOEOOE', 'EOOEOE' ];

  function padCodeUpce ( $text ) {

    if ( ! ctype_digit ( $text ) or ! in_array ( strlen ( $text ), [ 6, 7, 8, 11, 12 ] ) )
      return 'a upce barcode is 6 digits, 7 with the number system in front, 8 with the check digit behind - or the 11 or 12 digits of a UPC-A';

    // A UPC-A number is written in the short form whose expansion it is - when it has one.

    if ( strlen ( $text ) >= 11 ) {

      $upca = substr ( $text, 0, 11 );
      $long = padBarcodeDigits ( $text, 'upca', 12 );

      if ( ! is_array ( $long ) )
        return $long;

      $short = padCodeUpceShort ( $upca );

      if ( $short === '' )
        return "the UPC-A number $upca has no UPC-E form - it needs its zeros where UPC-E leaves them out";

      $text = $short . $long [0] [11];

    }

    if ( strlen ( $text ) == 6 )
      $text = "0$text";

    if ( $text [0] != '0' and $text [0] != '1' )
      return "the number system of a upce barcode is 0 or 1, not " . $text [0];

    $digits = substr ( $text, 1, 6 );
    $check  = padBarcodeCheck ( padCodeUpceExpand ( $text [0], $digits ) );

    if ( strlen ( $text ) == 8 and $text [7] != $check )
      return "the check digit of $text is $check, not " . $text [7];

    $parity = PAD_UPCE_PARITY [$check];
    $bars   = '101';

    for ( $i = 0; $i < 6; $i++ )
      $bars .= ( ( $parity [$i] == 'O' ) == ( $text [0] == '0' ) ) ? PAD_EAN_L [ $digits [$i] ] : PAD_EAN_G [ $digits [$i] ];

    $bars .= '010101';

    return [ $bars, [ [ -5, $text [0] ], [ 3 + 21, $digits ], [ 51 + 5, (string) $check ] ], [ [ 0, 3 ], [ 45, 51 ] ], 11 ];

  }

  // The UPC-A of a UPC-E: the last of the six digits says where the zeros go - after a
  // manufacturer number ending in 0, 1 or 2 (the digit), after three digits (3), after four
  // (4), or before a last product digit of 5 to 9.

  function padCodeUpceExpand ( $system, $d ) {

    switch ( $d [5] ) {
      case '0': case '1': case '2':
                 return $system . $d [0] . $d [1] . $d [5] . '0000' . $d [2] . $d [3] . $d [4];
      case '3':  return $system . $d [0] . $d [1] . $d [2] . '00000' . $d [3] . $d [4];
      case '4':  return $system . $d [0] . $d [1] . $d [2] . $d [3] . '00000' . $d [4];
      default:   return $system . $d [0] . $d [1] . $d [2] . $d [3] . $d [4] . '0000' . $d [5];
    }

  }

  // The number system and six digits of the UPC-E whose expansion is the UPC-A given, or ''
  // when no UPC-E expands to it: each of the four forms is tried and kept only when it
  // expands back to the same number.

  function padCodeUpceShort ( $upca ) {

    $system = $upca [0];

    if ( $system != '0' and $system != '1' )
      return '';

    $m = substr ( $upca, 1, 5 );
    $p = substr ( $upca, 6, 5 );

    foreach ( [ $m [0] . $m [1] . $p [2] . $p [3] . $p [4] . $m [2],
                $m [0] . $m [1] . $m [2] . $p [3] . $p [4] . '3',
                $m [0] . $m [1] . $m [2] . $m [3] . $p [4] . '4',
                $m [0] . $m [1] . $m [2] . $m [3] . $m [4] . $p [4] ] as $short )
      if ( padCodeUpceExpand ( $system, $short ) === $upca )
        return $system . $short;

    return '';

  }

?>
