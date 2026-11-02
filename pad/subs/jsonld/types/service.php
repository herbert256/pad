<?php
  return [ 'type' => 'Service', 'fields' => [
    'name' => [ 'name', 'text', TRUE ], 'description' => [ 'description', 'text' ],
    'service' => [ 'serviceType', 'text' ], 'url' => [ 'url', 'url' ],
    'provider' => [ 'provider.name', 'text' ], 'providerurl' => [ 'provider.url', 'url' ],
    'area' => [ 'areaServed', [ 'names', 'Place' ] ],
    'price' => [ 'offers.price', 'price' ], 'currency' => [ 'offers.priceCurrency', 'currency' ], 'offerurl' => [ 'offers.url', 'url' ],
  ], 'objects' => [ 'provider' => 'Organization', 'offers' => 'Offer' ], 'needs' => [ 'price' => 'currency' ] ];
?>
