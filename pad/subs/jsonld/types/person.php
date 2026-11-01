<?php

  // {jsonld:person name='Ann Smith', job='Engineer', profiles='https://github.com/ann'/} - a
  // Person: the author of a site, a speaker, a member of a team.
  //
  // The fields, by their PAD name and the Schema.org property each fills: name (name,
  // required), url (url, an address), image (image, an address), job (jobTitle), description
  // (description) and profiles (sameAs, the addresses of the person's pages elsewhere - a
  // list, or a text with a comma between them).

  return [

    'type'   => 'Person',

    'fields' => [
      'name'        => [ 'name',        'text', TRUE ],
      'url'         => [ 'url',         'url'        ],
      'image'       => [ 'image',       'url'        ],
      'job'         => [ 'jobTitle',    'text'       ],
      'description' => [ 'description', 'text'       ],
      'profiles'    => [ 'sameAs',      'urls'       ],
    ],

  ];

?>
