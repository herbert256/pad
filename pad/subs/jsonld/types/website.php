<?php

  // {jsonld:website name='PAD', search='?search&q='/} - a WebSite: the site itself, its name
  // and its address, and how to search it, which a search engine may offer as a search box
  // of its own.
  //
  // The fields, by their PAD name and the Schema.org property each fills: name (name), url
  // (url, an address - the application's own address when left out), description
  // (description) and search (potentialAction, a SearchAction): the address of the site's
  // search with the term at its end - ?search&q= - which is written as the action's
  // urlTemplate with {search_term_string} after it, braces that stay text. A search address
  // that holds {search_term_string} somewhere else - a value of the page's PHP - keeps it
  // where it stands.

  return [

    'type'   => 'WebSite',

    'fields' => [
      'name'        => [ 'name',            'text'                           ],
      'url'         => [ 'url',             'url',    FALSE, [], padJsonLdHome () ],
      'description' => [ 'description',     'text'                           ],
      'search'      => [ 'potentialAction', 'search'                         ],
    ],

  ];

?>
