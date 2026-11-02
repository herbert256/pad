<?php

  // book - Book metadata with named authors, a validated ISBN and edition details.

  return [ 'type' => 'Book', 'fields' => [
    'name' => [ 'name', 'text', TRUE, [ 'title' ] ], 'description' => [ 'description', 'text' ],
    'author' => [ 'author', [ 'names', 'Person' ] ], 'isbn' => [ 'isbn', [ 'isbn' ] ],
    'language' => [ 'inLanguage', 'text' ], 'edition' => [ 'bookEdition', 'text' ], 'pages' => [ 'numberOfPages', 'count' ],
    'published' => [ 'datePublished', 'date' ], 'publisher' => [ 'publisher.name', 'text' ],
    'url' => [ 'url', 'url' ], 'image' => [ 'image', 'url' ],
  ], 'objects' => [ 'publisher' => 'Organization' ] ];
?>
