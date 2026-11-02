<?php

  // The kinds are the files of pad/subs/jsonld/types/, as the engine reads them (padJsonLdKinds),
  // and the options beside data=, id= and home= are their fields, read from the kinds
  // themselves (padJsonLdSpec): a field added to a kind is an option here.

  include_once PAD . 'subs/jsonld/_lib/jsonld.php';

  $tagAbout   = 'Writes the structured data of a page - a <script type="application/ld+json"> holding a Schema.org object - for ' . count ( padJsonLdKinds () ) . ' kinds, from an article to a product, each field mapped by name, so nothing else of a row reaches the page.';
  $tagGroup   = 'navigation';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{jsonld:kind data='rows'/}
{jsonld:article headline=$title, author=$author, published=$date/}
{jsonld:article data='post', url=$url/}
{jsonld:faq data='questions'/}
{jsonld:breadcrumb/}
{jsonld:website name='Shop', search='?search&q='/}
PAD;

  $tagParms   = [
    'kind after colon' => 'The kind, a file of <code>pad/subs/jsonld/types/</code>: '
            . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", padJsonLdKinds () ) )
            . '. The jsonld: prefix selects it - <code>{jsonld:article .../}</code>; a kind is never reached bare.' ];

  $tagOptions = [
    'data' => 'The row or rows of the fields: a {pad:data} store, the page\'s array, a field holding a list or a _data file - one row of fields, or rows, which a kind of one object writes as a <code>@graph</code> and faq, breadcrumb and itemlist as their items. The handling options work on them.',
    'id'   => 'The object\'s <code>@id</code>, an address made absolute - <code>id=\'#org\'</code> - or an IRI of another scheme.',
    'home' => 'breadcrumb without data=: the text of the first step of the trail (Home), as the breadcrumb widget takes it.' ];

  // A field of every kind that takes it, with the Schema.org property it fills there: the
  // fields of the rows of faq, breadcrumb and itemlist are no options.

  $tagJsonLdFields = [];

  foreach ( padJsonLdKinds () as $tagJsonLdKind )
    foreach ( padJsonLdSpec ( $tagJsonLdKind ) ['fields'] as $tagJsonLdName => $tagJsonLdField )
      $tagJsonLdFields [$tagJsonLdName] [] = "$tagJsonLdKind (<code>{$tagJsonLdField ['path']}</code>"
                                           . ( $tagJsonLdField ['required'] ? ', required' : '' ) . ')';

  ksort ( $tagJsonLdFields );

  foreach ( $tagJsonLdFields as $tagJsonLdName => $tagJsonLdKinds )
    $tagOptions [$tagJsonLdName] = 'A field of ' . implode ( ', ', $tagJsonLdKinds ) . ' - it wins over the field of the row.';

  $tagSee     = [ 'sitemap', 'meta', 'widget:breadcrumb' ];

?>
