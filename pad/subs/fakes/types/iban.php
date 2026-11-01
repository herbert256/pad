<?php

  // iban - An international bank account number that passes the IBAN check - two check
  // digits by ISO 13616's modulo 97 - of a bank that is not there: a Dutch number of the
  // bank code TEST, so a form that checks the number takes it and no payment can ever reach
  // an account: NL13TEST0123456789, written without spaces.

  $account = '';

  for ( $i = 0; $i < 10; $i++ )
    $account .= padFakeNumber ( 0, 9 );

  $bban = "TEST$account";

  // The check digits: the account with NL00 behind it, every letter as its two digits
  // (A = 10 ... Z = 35), 98 less that number modulo 97 - worked through in parts, as the
  // whole is too long for an integer.

  $digits = '';

  foreach ( str_split ( $bban . 'NL00' ) as $char )
    $digits .= ctype_alpha ( $char ) ? (string) ( ord ( $char ) - 55 ) : $char;

  $rest = 0;

  foreach ( str_split ( $digits, 7 ) as $part )
    $rest = (int) ( $rest . $part ) % 97;

  return sprintf ( 'NL%02d', 98 - $rest ) . $bban;

?>
