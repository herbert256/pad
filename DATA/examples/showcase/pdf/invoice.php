<?php

  $invoice  = '2026-114';
  $issued   = '8 October 2026';
  $due      = '7 November 2026';
  $customer = [ 'name' => 'Noordkaap Sailing School', 'street' => 'Havenkade 12', 'city' => '1781 AB Den Helder' ];

  $lines = [
    [ 'item' => 'Mooring rope, 12 mm, per 20 m',     'qty' => 4,  'price' => 38.50 ],
    [ 'item' => 'Fender, inflatable, large',         'qty' => 6,  'price' => 27.95 ],
    [ 'item' => 'Life jacket 150 N, adult',          'qty' => 10, 'price' => 64.00 ],
    [ 'item' => 'Shackle, stainless, 8 mm',          'qty' => 24, 'price' => 3.20  ],
    [ 'item' => 'Delivery to the harbour',           'qty' => 1,  'price' => 45.00 ] ];

  $subtotal = 0;

  foreach ( $lines as $key => $line ) {
    $lines [$key] ['amount'] = $line ['qty'] * $line ['price'];
    $subtotal += $lines [$key] ['amount'];
  }

  $vat   = round ( $subtotal * 0.21, 2 );
  $total = $subtotal + $vat;

?>
