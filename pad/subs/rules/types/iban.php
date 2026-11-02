<?php

  // iban - an international bank account number whose check digits hold: the country's two
  // letters, two check digits and 11 to 30 letters and digits, the spaces it is often
  // written with left out, in either case; moved the first four to the end and read with
  // A as 10 to Z as 35, the number leaves 1 when divided by 97 (ISO 13616). It does not
  // know the length of each country's numbers - a typing error the checksum catches.

  return [
    'check'   => function ( $value ) {
      $iban = strtoupper ( str_replace ( ' ', '', $value ) );
      if ( ! preg_match ( '/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/D', $iban ) )
        return FALSE;
      $rest = 0;
      foreach ( str_split ( substr ( $iban, 4 ) . substr ( $iban, 0, 4 ) ) as $char )
        foreach ( str_split ( ctype_digit ( $char ) ? $char : (string) ( ord ( $char ) - 55 ) ) as $digit )
          $rest = ( $rest * 10 + (int) $digit ) % 97;
      return $rest == 1;
    },
    'message' => ':label must be a valid IBAN'
  ];

?>
