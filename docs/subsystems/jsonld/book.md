# Book metadata

`{jsonld:book data='record'/}` maps a record to [Schema.org Book](https://schema.org/Book). Required name (alias title); author (one or more named Persons), isbn, language (inLanguage), edition (bookEdition), pages, published, publisher, url and image. ISBN-10/13 validation reuses the isbn rule, verifies its checksum and removes spaces/hyphens. Invalid optional identifiers are reported and omitted in lenient mode; strict templates report an error. Supply authors as an array when names contain commas.

Only declared fields are exported. Existing URL filtering and script-safe JSON encoding apply. Schema vocabulary support does not guarantee search-engine rich-result eligibility. A complete record is in `apps/tags/examples/jsonld/book.php`. Run `php apps/regression/subsystems/jsonld-book.php`.
