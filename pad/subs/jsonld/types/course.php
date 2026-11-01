<?php

  // {jsonld:course data='course'/} or {jsonld:course name='PAD in a day', description='The
  // template language from the first tag to a shop.', provider='PAD Academy'/} - a Course:
  // a series of lessons, what it teaches and who gives it. Google shows courses as a list:
  // the course list rich result is the itemlist kind on the page that lists them, each
  // course's own page holding its course.
  //
  // The fields, by their PAD name and the Schema.org property each fills - the required and
  // recommended properties of Google's course list rich result: name (name, required - the
  // title alone, no price or offer in it), description (description, required - Google shows
  // its first 60 characters), provider (provider, an Organization by its name - the school
  // or the company whose course it is), providerurl (the provider's sameAs, its own site),
  // url (url), image (image, an address) and code (courseCode, the course's code at its
  // provider, CS101).

  return [

    'type'    => 'Course',

    'fields'  => [
      'name'        => [ 'name',            'text', TRUE ],
      'description' => [ 'description',     'text', TRUE ],
      'provider'    => [ 'provider.name',   'text'       ],
      'providerurl' => [ 'provider.sameAs', 'url'        ],
      'url'         => [ 'url',             'url'        ],
      'image'       => [ 'image',           'url'        ],
      'code'        => [ 'courseCode',      'text'       ],
    ],

    'objects' => [ 'provider' => 'Organization' ],

  ];

?>
