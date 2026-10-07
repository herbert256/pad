<?php

  $title = 'Barcodes';

  // A product list as a shop keeps it: the EAN-13 without its check digit for two of them -
  // the tag adds it - and with it for the others, where the tag checks it.

  $products = [
    [ 'name' => 'Espresso beans, 1 kg', 'ean' => '871234567890'  ],
    [ 'name' => 'Oat milk, 1 l',        'ean' => '4006381333931' ],
    [ 'name' => 'Design Patterns',      'ean' => '9780201633610' ],
    [ 'name' => 'Paper filters',        'ean' => '540123456789'  ],
  ];

  $parcel = 'PAD-2026-000042';

?>
