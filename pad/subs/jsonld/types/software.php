<?php

  // {jsonld:software data='app'/} or {jsonld:software name='Pocket Ledger', os='Android 12,
  // iOS 17', category='finance', price='0', rating='4.6', ratings='1523'/} - a
  // SoftwareApplication: a program, an app, a web application - what it runs on, what it is
  // for and what it costs.
  //
  // The fields, by their PAD name and the Schema.org property each fills - the required and
  // recommended properties of Google's software app rich result: type (the @type:
  // SoftwareApplication when left out, MobileApplication or WebApplication), name (name,
  // required), description (description), url (url), image (image, an address), os
  // (operatingSystem, Windows 11, Android 12), category (applicationCategory, one of the 22
  // Google takes - GameApplication, FinanceApplication, DeveloperApplication ... in any case
  // or spacing, finance application), version (softwareVersion), download (downloadUrl),
  // price (offers.price, required - 0 for one that is free), currency (offers.priceCurrency,
  // ISO 4217 - for a price above 0), rating and ratings (aggregateRating, the average score
  // and the number of ratings - the one no use without the other). Google shows the rich
  // result only for an app with a rating or a review.

  return [

    'type'    => 'SoftwareApplication',

    'fields'  => [
      'type'        => [ '@type',                        [ 'word', [ 'SoftwareApplication', 'MobileApplication', 'WebApplication' ] ] ],
      'name'        => [ 'name',                         'text',  TRUE ],
      'description' => [ 'description',                  'text'        ],
      'url'         => [ 'url',                          'url'         ],
      'image'       => [ 'image',                        'url'         ],
      'os'          => [ 'operatingSystem',              'text'        ],
      'category'    => [ 'applicationCategory',          [ 'word', [
        'GameApplication', 'SocialNetworkingApplication', 'TravelApplication', 'ShoppingApplication', 'SportsApplication',
        'LifestyleApplication', 'BusinessApplication', 'DesignApplication', 'DeveloperApplication', 'DriverApplication',
        'EducationalApplication', 'HealthApplication', 'FinanceApplication', 'SecurityApplication', 'BrowserApplication',
        'CommunicationApplication', 'DesktopEnhancementApplication', 'EntertainmentApplication', 'MultimediaApplication',
        'HomeApplication', 'UtilitiesApplication', 'ReferenceApplication' ] ] ],
      'version'     => [ 'softwareVersion',              'text'        ],
      'download'    => [ 'downloadUrl',                  'url'         ],
      'price'       => [ 'offers.price',                 'price', TRUE ],
      'currency'    => [ 'offers.priceCurrency',         'currency'    ],
      'rating'      => [ 'aggregateRating.ratingValue',  'number'      ],
      'ratings'     => [ 'aggregateRating.ratingCount',  'count'       ],
    ],

    'objects' => [ 'offers' => 'Offer', 'aggregateRating' => 'AggregateRating' ],

    'needs'   => [ 'rating' => 'ratings', 'ratings' => 'rating' ],

  ];

?>
