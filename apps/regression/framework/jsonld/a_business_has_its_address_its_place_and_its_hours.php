<?php

  // A shop as a database answers it - its address, its place on the map as numbers, its hours
  // as openingHours writes them, and its owner's password, which never reaches the page.

  $shop = [ 'name' => 'The Loaf', 'description' => 'Bread from the wood oven.', 'image' => 'loaf.jpg',
            'url' => '?shops/loaf', 'telephone' => '+31 30 123 4567', 'street' => 'Main Street 1', 'city' => 'Utrecht',
            'postcode' => '3511 AA', 'country' => 'NL', 'latitude' => 52.09074, 'longitude' => '5.12142',
            'hours' => 'Mo-Fr 08:00-18:00; Sa 08:00-16:00', 'prices' => '$$', 'password' => 'hunter2' ];

?>
