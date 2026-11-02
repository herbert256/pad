<?php

  // The ISBN of a book as its barcode, the Bookland EAN: the ISBN-13 - 978 or 979 and ten more
  // digits - drawn as an EAN-13, the ISBN written above the bars as the ISBN Users' Manual asks,
  // with the hyphens it was given. An ISBN-10 becomes the ISBN-13 978 and its first nine digits
  // and the check digit of EAN-13, its hyphens kept where they stood; its own check digit -
  // weights 10 to 1 summing to a multiple of 11, X for 10 - is checked first. "ISBN" in front,
  // spaces and hyphens are allowed; the price goes in an add-on of five digits (addon=).
  //
  //   {barcode:bookland '978-0-306-40615-7'/}
  //   {barcode:bookland 'ISBN 0-306-40615-2', addon='51995'/}       978-0-306-40615-7, US$ 19.95
  //
  // padCodeIsbn   [ the thirteen digits, the ISBN to write above ], or the message why not

  function padCodeIsbn ( $text ) {

    $isbn   = trim ( preg_replace ( '/^ISBN(-1[03])?:?\s*/i', '', trim ( $text ) ) );
    $digits = strtoupper ( str_replace ( [ '-', ' ' ], '', $isbn ) );
    $form   = "a bookland barcode is an ISBN - 13 digits starting with 978 or 979, or the 10 of an ISBN-10";

    if ( preg_match ( '/^\d{9}[\dX]$/', $digits ) ) {

      $sum = 0;
      for ( $i = 0; $i < 10; $i++ )
        $sum += ( 10 - $i ) * ( $digits [$i] == 'X' ? 10 : (int) $digits [$i] );

      if ( $sum % 11 ) {
        $want = ( 11 - ( $sum - ( $digits [9] == 'X' ? 10 : (int) $digits [9] ) ) % 11 ) % 11;
        return "the check digit of the ISBN-10 $isbn is " . ( $want == 10 ? 'X' : $want ) . ', not ' . $digits [9];
      }

      $ean  = '978' . substr ( $digits, 0, 9 );
      $ean .= padBarcodeCheck ( $ean );

      $shown = preg_match ( '/^[\dXx]+(-[\dXx]+)+$/', $isbn )
        ? '978-' . substr ( $isbn, 0, -1 ) . $ean [12]
        : $ean;

      return [ $ean, 'ISBN ' . $shown ];

    }

    if ( ! preg_match ( '/^97[89]\d{10}$/', $digits ) )
      return $form;

    $check = padBarcodeCheck ( substr ( $digits, 0, 12 ) );

    if ( $digits [12] != $check )
      return "the check digit of the ISBN $isbn is $check, not " . $digits [12];

    return [ $digits, 'ISBN ' . ( preg_match ( '/^\d+(-\d+)+$/', $isbn ) ? $isbn : $digits ) ];

  }

?>
