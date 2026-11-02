<?php

  // Code 39, the barcode of industry and of the warehouse label: digits, capital letters,
  // space and - . $ / + %, each written as nine elements - five bars and the four spaces
  // between them - of which three are wide, between the start and stop symbol *. A wide
  // element is three modules, a narrow one one, and a narrow space stands between symbols.
  //
  //   {barcode:code39 'PAD-2026'/}
  //   {barcode:code39 'PAD-2026', check/}       with the check symbol of modulo 43 behind the text
  //
  // padCode39   the bars, or the message why the text is no Code 39

  const PAD_CODE39_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-. $/+%';

  // The nine elements of each symbol of PAD_CODE39_CHARS, and of * last: a bit per element,
  // the first bar first, 1 a wide one.

  const PAD_CODE39 = [
    0x034, 0x121, 0x061, 0x160, 0x031, 0x130, 0x070, 0x025, 0x124, 0x064,
    0x109, 0x049, 0x148, 0x019, 0x118, 0x058, 0x00D, 0x10C, 0x04C, 0x01C,
    0x103, 0x043, 0x142, 0x013, 0x112, 0x052, 0x007, 0x106, 0x046, 0x016,
    0x181, 0x0C1, 0x1C0, 0x091, 0x190, 0x0D0, 0x085, 0x184, 0x0C4, 0x0A8,
    0x0A2, 0x08A, 0x02A, 0x094 ];

  function padCode39 ( $text, $check ) {

    if ( $text === '' or strspn ( $text, PAD_CODE39_CHARS ) != strlen ( $text ) )
      return 'a code39 barcode takes digits, capital letters, space and - . $ / + % - code128 or code93 take the rest of ASCII';

    $values = array_map ( fn ( $char ) => strpos ( PAD_CODE39_CHARS, $char ), str_split ( $text ) );

    if ( $check )
      $values [] = array_sum ( $values ) % 43;

    $bars = '';

    foreach ( array_merge ( [ 43 ], $values, [ 43 ] ) as $k => $value )
      $bars .= ( $k ? '0' : '' ) . padCodeElements ( PAD_CODE39 [$value], 9, 3 );

    return [ $bars, [ [ strlen ( $bars ) / 2, $text ] ], [], 10 ];

  }

?>
