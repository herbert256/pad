<?php

  // Code 93, the denser successor of Code 39: every printable ASCII character, each symbol nine
  // modules - three bars and three spaces of one to four modules - between the start and stop
  // symbol, two check symbols of modulo 47 before the stop, and a last bar that closes it. The
  // 43 characters of Code 39 have a symbol each; the rest of ASCII is a pair, one of the four
  // shift symbols and a letter, as the full ASCII table of Code 93 says.
  //
  //   {code93 'Pad-2026'/}
  //
  // padCode93   the bars, or the message why the text is no Code 93

  const PAD_CODE93_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-. $/+%';

  // The nine modules of each symbol: the 43 of PAD_CODE93_CHARS, the shift symbols ($) (%) (/)
  // and (+) - values 43 to 46 - and the start and stop symbol last; a bit per module, 1 a bar.

  const PAD_CODE93 = [
    0x114, 0x148, 0x144, 0x142, 0x128, 0x124, 0x122, 0x150, 0x112, 0x10A,
    0x1A8, 0x1A4, 0x1A2, 0x194, 0x192, 0x18A, 0x168, 0x164, 0x162, 0x134,
    0x11A, 0x158, 0x14C, 0x146, 0x12C, 0x116, 0x1B4, 0x1B2, 0x1AC, 0x1A6,
    0x196, 0x19A, 0x16C, 0x166, 0x136, 0x13A,
    0x12E, 0x1D4, 0x1D2, 0x1CA, 0x16E, 0x176, 0x1AE,
    0x126, 0x1DA, 0x1D6, 0x132, 0x15E ];

  function padCode93 ( $text ) {

    if ( $text === '' or preg_match ( '/[^\x20-\x7E]/', $text ) )
      return 'a code93 barcode takes printable ASCII - letters, digits, space and punctuation';

    $values = [];

    foreach ( str_split ( $text ) as $char )
      foreach ( padCode93Symbols ( $char ) as $value )
        $values [] = $value;

    // The check symbols: C of the data, weighted 1 to 20 from the right, then K of the data
    // and C, weighted 1 to 15 from the right - both modulo 47.

    foreach ( [ 20, 15 ] as $cycle ) {
      $sum = 0;
      foreach ( array_reverse ( $values ) as $i => $value )
        $sum += $value * ( $i % $cycle + 1 );
      $values [] = $sum % 47;
    }

    $bars = '';

    foreach ( array_merge ( [ 47 ], $values, [ 47 ] ) as $value )
      $bars .= sprintf ( '%09b', PAD_CODE93 [$value] );

    return [ $bars . '1', [ [ ( strlen ( $bars ) + 1 ) / 2, $text ] ], [], 10 ];

  }

  // The symbols of a character: its own when Code 39 has it, else a shift symbol and a letter
  // - ($) the control characters, (%) and (/) punctuation, (+) the small letters.

  function padCode93Symbols ( $char ) {

    $code = ord ( $char );

    if ( ( $at = strpos ( PAD_CODE93_CHARS, $char ) ) !== FALSE )
      return [ $at ];

    $letter = fn ( $shift, $from, $first ) => [ $shift, strpos ( PAD_CODE93_CHARS, chr ( ord ( $first ) + $code - $from ) ) ];

    if ( $code <= 44 ) return $letter ( 45, 33, 'A' );        // ! " # & ' ( ) * ,  as (/)A - (/)L
    if ( $code == 58 ) return [ 45, strpos ( PAD_CODE93_CHARS, 'Z' ) ];
    if ( $code <= 63 ) return $letter ( 44, 59, 'F' );        // ; < = > ?          as (%)F - (%)J
    if ( $code == 64 ) return [ 44, strpos ( PAD_CODE93_CHARS, 'V' ) ];
    if ( $code <= 95 ) return $letter ( 44, 91, 'K' );        // [ \ ] ^ _          as (%)K - (%)O
    if ( $code == 96 ) return [ 44, strpos ( PAD_CODE93_CHARS, 'W' ) ];
    if ( $code <= 122 ) return $letter ( 46, 97, 'A' );       // a - z              as (+)A - (+)Z

    return $letter ( 44, 123, 'P' );                          // { | } ~            as (%)P - (%)S

  }

?>
