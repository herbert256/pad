<?php

  // {jsonld:breadcrumb/} or {jsonld:breadcrumb data='trail'/} - a BreadcrumbList: where the
  // page stands, from the home page down, each step a ListItem numbered by its position.
  //
  // Alone it is the trail the breadcrumb widget {breadcrumb/} shows for the page - Home, every
  // directory of the page's path and the page itself, home= naming the first step - read from
  // the widget, so the two say the same, but for a directory that is no page, which has no
  // address to give and is left out. With data= the steps are its rows, the rows the
  // widget's data= takes as well: name (name, required - or label, text) and url (item, an
  // address - or link, href). A step without an address is written without one, as the last
  // step may be.

  return [

    'type'  => 'BreadcrumbList',

    'items' => [
      'path'     => 'itemListElement',
      'type'     => 'ListItem',
      'position' => TRUE,
      'fields'   => [
        'name' => [ 'name', 'text', TRUE, [ 'label', 'text' ] ],
        'url'  => [ 'item', 'url',  FALSE, [ 'link', 'href' ] ],
      ],
    ],

    'trail' => TRUE,

  ];

?>
