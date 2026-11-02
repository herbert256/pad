<?php

  // The object as a plain array - an API answer, a page's own JSON - and only the fields the
  // kind maps.

  $data = padJsonLd ( 'product', [ 'name' => 'Kettle', 'price' => '39.95', 'currency' => 'eur', 'cost' => '11.00' ], 'https://shop.example/kettle' );
  $keys = implode ( ',', array_keys ( $data ) );
  $json = json_encode ( $data, JSON_UNESCAPED_SLASHES );

?>
