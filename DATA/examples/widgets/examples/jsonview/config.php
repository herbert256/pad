<?php

  // The settings of a shop, as the page's PHP holds them.

  $settings = [
    'shop'     => [ 'name' => 'Mini Wheels Co.', 'currency' => 'EUR', 'locales' => [ 'en', 'nl', 'de' ] ],
    'mail'     => [ 'transport' => 'smtp', 'host' => 'mail.example.com', 'port' => 587, 'tls' => TRUE ],
    'cache'    => [ 'pages' => TRUE, 'ttl' => 600, 'store' => 'apcu' ],
    'features' => [ 'reviews' => TRUE, 'wishlist' => FALSE, 'giftcards' => NULL ],
    'version'  => '3.0.2',
  ];

?>
