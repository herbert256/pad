<?php

  // Type handler for a JSON-LD block: {jsonld:article data='post'/}, the jsonld: prefix naming
  // a file of subs/jsonld/types/ - never a bare name, the kinds' names (article, event,
  // person, product) being those of fields and pages. The kind is the tag's name, and the
  // block is written as {jsonld 'article', data='post'/} writes it, by subs/jsonld/jsonld.php -
  // run as a built-in tag's PHP half is run, types/_go/tag.php.

  $padJsonLdKind = $padTag [$pad];

  $padTagGo = PAD . 'subs/jsonld/jsonld';

  return include PAD . 'types/_go/tag.php';

?>
