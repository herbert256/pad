<?php

  $order = [
    'number'   => 1042,
    'customer' => [ 'name'    => 'Ann',
                    'address' => [ 'city' => 'Leiden' ] ],
    'lines'    => [ [ 'sku' => 'TEA', 'qty' => 2 ],
                    [ 'sku' => 'MUG', 'qty' => 1 ] ]
  ];

  $city = padArrGet ( $order, 'customer.address.city' );
  $zip  = padArrGet ( $order, 'customer.address.zip',
                      'not known' );
  $skus = padArrGet ( $order, 'lines.*.sku' );

  $products = implode ( ', ', $skus );

  padArrSet    ( $order, 'customer.address.zip', '2311 AB' );
  padArrForget ( $order, 'lines.1' );

  $hasZip   = padArrHas ( $order, 'customer.address.zip' )
            ? 'yes' : 'no';
  $zipNow   = padArrGet ( $order, 'customer.address.zip' );
  $lineLeft = padArrGet ( $order, 'lines.0.sku' );

?>
