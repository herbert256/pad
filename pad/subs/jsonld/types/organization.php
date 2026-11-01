<?php

  // {jsonld:organization name='PAD', url='https://pad.example', logo='logo.png'/} - an
  // Organization: the company, the club or the project behind the site, its logo and its
  // profiles elsewhere.
  //
  // The fields, by their PAD name and the Schema.org property each fills: name (name,
  // required), url (url, an address), logo (logo, an address), description (description),
  // profiles (sameAs, the addresses of its pages elsewhere - a list, or a text with a comma
  // between them), and its contact as a ContactPoint: phone (telephone), email (email) and
  // contact (contactType, what the contact is for - customer service, sales).

  return [

    'type'    => 'Organization',

    'fields'  => [
      'name'        => [ 'name',                     'text', TRUE ],
      'url'         => [ 'url',                      'url'        ],
      'logo'        => [ 'logo',                     'url'        ],
      'description' => [ 'description',              'text'       ],
      'profiles'    => [ 'sameAs',                   'urls'       ],
      'phone'       => [ 'contactPoint.telephone',   'text'       ],
      'email'       => [ 'contactPoint.email',       'text'       ],
      'contact'     => [ 'contactPoint.contactType', 'text'       ],
    ],

    'objects' => [ 'contactPoint' => 'ContactPoint' ],

  ];

?>
