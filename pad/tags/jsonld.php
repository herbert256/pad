<?php

  // {jsonld 'article', data='post'/} - the structured data of a page: one
  // <script type="application/ld+json"> holding a Schema.org object built from the fields a
  // kind maps, written by the JSON-LD subsystem in subs/jsonld/: a kind is a file of
  // subs/jsonld/types/, the same block as the jsonld: type writes it, {jsonld:article
  // data='post'/}.
  //
  //   {jsonld 'article', headline=$title, author=$author, published=$date/}
  //   {jsonld 'faq', data='questions'/}
  //   {jsonld 'breadcrumb'/}
  //
  // padJsonLd ( 'article', $post ) makes the same object in a page's PHP, as a plain array.

  $padJsonLdKind = is_string ( $padParm ) ? strtolower ( trim ( $padParm ) ) : '';

  return include PAD . 'subs/jsonld/jsonld.php';

?>
