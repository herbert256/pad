<?php

  // {jsonld:itemlist data='recipes'/} - an ItemList: the page that lists pages of one kind -
  // the recipes of a category, the courses of a school, the restaurants of a town - each a
  // ListItem numbered by its position, with the address of its own page. With the recipe,
  // course, restaurant or a movie on each of those pages, Google shows the list as a
  // carousel (the summary page of its carousel documentation).
  //
  // The rows of data= are the items: url (url, required - or link, href: the address of the
  // item's own page, made absolute; Google wants each one once, on the site's own host) and
  // name (name - or label, title). A list of plain values is a list of addresses. Beside
  // the rows, name= is the name of the list itself (name). Google shows a carousel of two
  // items or more, all of one kind.

  return [

    'type'   => 'ItemList',

    'fields' => [
      'name' => [ 'name', 'text' ],
    ],

    'items'  => [
      'path'     => 'itemListElement',
      'type'     => 'ListItem',
      'position' => TRUE,
      'fields'   => [
        'url'  => [ 'url',  'url',  TRUE, [ 'link', 'href' ] ],
        'name' => [ 'name', 'text', FALSE, [ 'label', 'title' ] ],
      ],
    ],

  ];

?>
