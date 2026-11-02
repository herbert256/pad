# Dataset metadata

`{jsonld:dataset data='record'/}` maps a record to [Schema.org Dataset](https://schema.org/Dataset). Required name and description; optional creator/creatorurl (Organization), license, url, keywords, published/modified dates and distribution. Distribution is one download row or a list of at most 100, each requiring contentUrl (alias url); only name, description, encodingFormat and contentSize are also exported. Other fields stay private.

Only declared fields are exported. Existing URL filtering and script-safe JSON encoding apply. Schema vocabulary support does not guarantee search-engine rich-result eligibility. A complete record is in `apps/tags/examples/jsonld/dataset.php`. Run `php tests/subsystems/jsonld-dataset.php`.
