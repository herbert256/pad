<?php

  // padFake ( 'isbn' ) is an ISBN-13 of 978 and padFake ( 'ean' ) an EAN-13 of the GS1
  // prefixes 20-29, restricted circulation, each with the check digit of its standard: checked
  // here by a sum of their own - every digit weighed 1, 3, 1, 3 ... from the left, the check
  // digit too, a multiple of ten - which holds for the examples of the ISBN Users' Manual and
  // the GS1 General Specifications and fails for them with the last digit changed, and by the
  // barcode subsystem's EAN-13 check, which refuses a {barcode:ean13} with a wrong one.

  include_once PAD . 'subs/codes/_lib/barcode.php';

  $valid = function ( $digits ) {
    $sum = 0;
    foreach ( str_split ( $digits ) as $at => $digit )
      $sum += (int) $digit * ( $at % 2 ? 3 : 1 );
    return strlen ( $digits ) == 13 and ctype_digit ( $digits ) and $sum % 10 == 0;
  };

  padFakeSeed ( 6 );

  $isbns = $eans = $drawn = 0;
  $prefixes = [];

  for ( $i = 0; $i < 1000; $i++ ) {

    $isbn = padFake ( 'isbn' );
    $ean  = padFake ( 'ean' );

    $isbns += ( $valid ( $isbn ) and str_starts_with ( $isbn, '978' ) ) ? 1 : 0;
    $eans  += ( $valid ( $ean ) and $ean [0] == '2' ) ? 1 : 0;
    $drawn += ( padBarcodeDigits ( $ean, 'EAN-13', 13 ) === [ $ean ] and padBarcodeDigits ( $isbn, 'EAN-13', 13 ) === [ $isbn ] ) ? 1 : 0;

    $prefixes [ substr ( $ean, 0, 2 ) ] = TRUE;

  }

  padFakeSeed ( 'b' );  $first  = padFake ( 'isbn' ) . ' ' . padFake ( 'ean' );
  padFakeSeed ( 'b' );  $second = padFake ( 'isbn' ) . ' ' . padFake ( 'ean' );

  $r = json_encode ( [
    'checker'  => [ $valid ( '9780306406157' ), $valid ( '6291041500213' ), $valid ( '9780306406158' ), $valid ( '6291041500214' ) ],
    'isbn'     => $isbns,
    'ean'      => $eans,
    'barcode'  => $drawn,
    'prefixes' => count ( $prefixes ),
    'seed'     => $first === $second,
  ] );

?>
