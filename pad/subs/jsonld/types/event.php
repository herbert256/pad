<?php

  // {jsonld:event data='concert'/} or {jsonld:event name='PAD day', starts='2026-11-20 19:30',
  // place='The Hall', address='Main Street 1, Utrecht'/} - an Event: a concert, a course, a
  // meeting, when it starts and where, and the tickets for it.
  //
  // The fields, by their PAD name and the Schema.org property each fills: name (name,
  // required), starts (startDate, a date, required), ends (endDate, a date), place (location,
  // a Place by its name), address (the address of the location), description (description),
  // image (image, an address), url (url), and the tickets as an Offer: price (offers.price, a
  // number), currency (offers.priceCurrency, ISO 4217 - needed with a price), availability
  // (offers.availability, InStock, SoldOut, PreOrder ...) and tickets (offers.url, where to
  // buy them). Not start= and end=: those are the handling options every tag takes, the rows
  // from start to end - a row's or an option's startDate and endDate are read as starts and
  // ends.

  return [

    'type'    => 'Event',

    'fields'  => [
      'name'         => [ 'name',                 'text',         TRUE ],
      'starts'       => [ 'startDate',            'date',         TRUE ],
      'ends'         => [ 'endDate',              'date'               ],
      'place'        => [ 'location.name',        'text'               ],
      'address'      => [ 'location.address',     'text'               ],
      'description'  => [ 'description',          'text'               ],
      'image'        => [ 'image',                'url'                ],
      'url'          => [ 'url',                  'url'                ],
      'price'        => [ 'offers.price',         'price'              ],
      'currency'     => [ 'offers.priceCurrency', 'currency'           ],
      'availability' => [ 'offers.availability',  'availability'       ],
      'tickets'      => [ 'offers.url',           'url'                ],
    ],

    'objects' => [ 'location' => 'Place', 'offers' => 'Offer' ],

    'needs'   => [ 'price' => 'currency' ],

  ];

?>
