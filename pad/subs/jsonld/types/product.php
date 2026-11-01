<?php

  // {jsonld:product data='product'/} or {jsonld:product name='Kettle', price=$price,
  // currency='EUR', availability='InStock'/} - a Product: a thing for sale, what it is and
  // what it costs.
  //
  // The fields, by their PAD name and the Schema.org property each fills: name (name,
  // required), description (description), image (image, an address), url (url), sku (sku),
  // brand (brand, a Brand by its name), and the price as an Offer: price (offers.price, a
  // number), currency (offers.priceCurrency, ISO 4217 - needed with a price) and availability
  // (offers.availability, InStock, OutOfStock, PreOrder ...).

  return [

    'type'    => 'Product',

    'fields'  => [
      'name'         => [ 'name',                 'text',         TRUE ],
      'description'  => [ 'description',          'text'               ],
      'image'        => [ 'image',                'url'                ],
      'url'          => [ 'url',                  'url'                ],
      'sku'          => [ 'sku',                  'text'               ],
      'brand'        => [ 'brand.name',           'text'               ],
      'price'        => [ 'offers.price',         'price'              ],
      'currency'     => [ 'offers.priceCurrency', 'currency'           ],
      'availability' => [ 'offers.availability',  'availability'       ],
    ],

    'objects' => [ 'brand' => 'Brand', 'offers' => 'Offer' ],

    'needs'   => [ 'price' => 'currency' ],

  ];

?>
