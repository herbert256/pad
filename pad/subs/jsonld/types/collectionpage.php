<?php

  // collectionpage - Maps explicit fields to [Schema.org CollectionPage](https://schema.org/CollectionPage), checked against primary vocabulary on 2026-11-02. URL and date validation and script-safe JSON use the shared JSON-LD engine. Unlisted fields are omitted. `name` and `url` are required. `image` becomes primaryImageOfPage.contentUrl; `parent` becomes isPartOf.url. Optional description, published, modified and language. No search-engine eligibility claim.
  //
  // ## Example
  //
  // ```pad
  // {jsonld:collectionpage name='Guides', url='https://example.org/guides', description='Practical guides'/}
  // ```
  //
  // Run the focused fixture with `php tests/subsystems/jsonld-collectionpage.php`.

return ['type'=>'CollectionPage','fields'=>['name'=>['name','text',TRUE], 'url'=>['url','url',TRUE], 'description'=>['description','text'], 'published'=>['datePublished','date'], 'modified'=>['dateModified','date'], 'language'=>['inLanguage','text'], 'image'=>['primaryImageOfPage.contentUrl','url'], 'parent'=>['isPartOf.url','url']],'objects'=>['primaryImageOfPage'=>'ImageObject','isPartOf'=>'WebSite']];
