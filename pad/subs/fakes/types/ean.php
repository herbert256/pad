<?php

  // ean - An EAN-13 (a GTIN-13) whose check digit holds, in the GS1 prefixes 20 to 29 the GS1
  // General Specifications keep for restricted circulation - numbers a shop gives its own
  // goods, never a product's number in the open supply chain - twelve digits and the check
  // digit GS1 sets, weights 3 and 1 from the right: 2860425176463, one {barcode:ean13} draws.

  $digits = '2';

  for ( $i = 0; $i < 11; $i++ )
    $digits .= padFakeNumber ( 0, 9 );

  $sum = 0;

  foreach ( str_split ( strrev ( $digits ) ) as $at => $digit )
    $sum += (int) $digit * ( $at % 2 ? 1 : 3 );

  return $digits . ( ( 10 - $sum % 10 ) % 10 );

?>
