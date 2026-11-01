<?php

  // Interleaved 2 of 5, the barcode of digits on cartons and tickets: the digits two at a time,
  // the first of a pair written in the five bars of a symbol and the second in the five spaces
  // between them, two of each five wide - three modules to a narrow one's one. A start of four
  // narrow elements opens it, a wide bar, a narrow space and a narrow bar close it. An odd
  // number of digits gets a 0 in front.
  //
  //   {itf '12345670'/}
  //   {itf '1234567', check/}           the check digit of weights 3 and 1 added
  //   {itf14 '1540014128876'/}          ITF-14, the number of a carton (subs/codes/types/itf14.php)
  //
  // padCodeItf   the bars, or the message why the text is no ITF

  // The five elements of each digit, a bit per element, 1 a wide one.

  const PAD_ITF = [ 0b00110, 0b10001, 0b01001, 0b11000, 0b00101, 0b10100, 0b01100, 0b00011, 0b10010, 0b01010 ];

  function padCodeItf ( $digits, $bearer = 0 ) {

    $bars = '1010';

    for ( $i = 0; $i < strlen ( $digits ); $i += 2 ) {
      $black = PAD_ITF [ $digits [$i] ];
      $white = PAD_ITF [ $digits [$i + 1] ];
      for ( $bit = 4; $bit >= 0; $bit-- )
        $bars .= str_repeat ( '1', ( $black >> $bit & 1 ) ? 3 : 1 ) . str_repeat ( '0', ( $white >> $bit & 1 ) ? 3 : 1 );
    }

    $bars .= '111' . '0' . '1';

    return [ $bars, [ [ strlen ( $bars ) / 2, $digits ] ], [], 10, $bearer ];

  }

  // The digits of an ITF: the check digit added when asked, and a 0 in front of an odd number
  // of digits - which leaves the check digit as it was, its weights counted from the right.

  function padCodeItfDigits ( $text, $check ) {

    if ( $text === '' or ! ctype_digit ( $text ) )
      return 'an itf barcode takes digits only';

    if ( $check )
      $text .= padBarcodeCheck ( $text );

    return ( strlen ( $text ) % 2 ) ? "0$text" : $text;

  }

?>
