<?php

  // dataset - Dataset metadata with a named creator, license and explicitly validated downloads.

  return [ 'type' => 'Dataset', 'fields' => [
    'name' => [ 'name', 'text', TRUE ], 'description' => [ 'description', 'text', TRUE ],
    'url' => [ 'url', 'url' ], 'creator' => [ 'creator.name', 'text' ], 'creatorurl' => [ 'creator.url', 'url' ],
    'license' => [ 'license', 'url' ], 'keywords' => [ 'keywords', 'texts' ],
    'published' => [ 'datePublished', 'date' ], 'modified' => [ 'dateModified', 'date' ],
    'distribution' => [ 'distribution', [ 'downloads' ] ],
  ], 'objects' => [ 'creator' => 'Organization' ] ];
?>
