<?php

  // {jsonld:article data='post'/} or {jsonld:article headline=$title, author=$author,
  // published=$date/} - an Article: a post of a blog, a news item, a page of a magazine, its
  // headline, who wrote it and when.
  //
  // The fields, by their PAD name and the Schema.org property each fills: headline
  // (headline, required), description (description), image (image, an address), url (url),
  // author (author, a Person by its name), published (datePublished, a date), modified
  // (dateModified, a date), publisher (publisher, an Organization by its name) and logo (the
  // publisher's logo, an address). A row's field or an option of the Schema.org name -
  // datePublished - is read as its PAD name.

  return [

    'type'    => 'Article',

    'fields'  => [
      'headline'    => [ 'headline',       'text', TRUE ],
      'description' => [ 'description',    'text'       ],
      'image'       => [ 'image',          'url'        ],
      'url'         => [ 'url',            'url'        ],
      'author'      => [ 'author.name',    'text'       ],
      'published'   => [ 'datePublished',  'date'       ],
      'modified'    => [ 'dateModified',   'date'       ],
      'publisher'   => [ 'publisher.name', 'text'       ],
      'logo'        => [ 'publisher.logo', 'url'        ],
    ],

    'objects' => [ 'author' => 'Person', 'publisher' => 'Organization' ],

  ];

?>
