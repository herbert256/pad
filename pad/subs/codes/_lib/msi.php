<?php

  // MSI, the Plessey code of shelf labels: digits only, each written as its four bits, the
  // highest first - a 1 a wide bar and a narrow space, a 0 a narrow bar and a wide space, wide
  // two modules to a narrow one's one - between a start of a wide bar and a stop of a narrow
  // bar between two spaces. One check digit of modulo 10 (Luhn's) is added unless check= says
  // otherwise: mod1010 adds a second one of the same kind, none none.
  //
  //   {msi '1234567'/}                  12345674
  //   {msi '1234567', check='mod1010'/} 123456741
  //
  // padCodeMsi        the bars, or the message why the text is no MSI
  // padCodeMsiCheck   the check digit of modulo 10

  const PAD_MSI_CHECKS = [ 'mod10', 'mod1010', 'none' ];

  function padCodeMsi ( $text, $check ) {

    if ( $text === '' or ! ctype_digit ( $text ) )
      return 'an msi barcode takes digits only';

    if ( ! in_array ( $check, PAD_MSI_CHECKS, TRUE ) )
      return "an msi barcode has no check '" . padMakeSafe ( $check, 10 ) . "' - mod10, mod1010 or none";

    if ( $check != 'none' )
      $text .= padCodeMsiCheck ( $text );

    if ( $check == 'mod1010' )
      $text .= padCodeMsiCheck ( $text );

    $bars = '110';

    foreach ( str_split ( $text ) as $digit )
      foreach ( str_split ( sprintf ( '%04b', $digit ) ) as $bit )
        $bars .= $bit ? '110' : '100';

    $bars .= '1001';

    return [ $bars, [ [ strlen ( $bars ) / 2, $text ] ], [], 12 ];

  }

  // Luhn's check digit: from the right, every other digit doubled - the last one first - and
  // the digits of the products added up.

  function padCodeMsiCheck ( $digits ) {

    $sum = 0;

    foreach ( array_reverse ( str_split ( $digits ) ) as $i => $digit ) {
      $value = $i % 2 ? (int) $digit : 2 * $digit;
      $sum  += intdiv ( $value, 10 ) + $value % 10;
    }

    return ( 10 - $sum % 10 ) % 10;

  }

?>
