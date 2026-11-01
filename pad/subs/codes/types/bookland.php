<?php

  // bookland - the barcode of a book, its ISBN as an EAN-13 (Bookland EAN): an ISBN-13, or an
  // ISBN-10 made one (978 in front, the EAN-13 check digit behind), its check digit checked,
  // "ISBN" in front and hyphens allowed, the ISBN written above the bars with the hyphens it
  // was given (subs/codes/_lib/isbn.php). Options: addon= the five digits of the price add-on
  // - 5 and the price in US dollars, 0 and 1 pounds sterling, 90000 none - or two; height,
  // scale, title, plain, color, background.

  include_once PAD . 'subs/codes/_lib/isbn.php';
  include_once PAD . 'subs/codes/_lib/addon.php';

  $padIsbn  = padCodeIsbn ( padCodeText () );
  $padAddon = padTagParm ( 'addon' );

  if ( is_array ( $padIsbn ) ) {
    $padIsbnCoded      = padBarcodeModules ( $padIsbn [0], 'ean13' );
    $padIsbnCoded [6]  = $padIsbn [1];
    $padIsbn           = $padIsbnCoded;
  }

  return padCodeLinear ( padCodeAddon ( $padIsbn, $padAddon ) );

?>
