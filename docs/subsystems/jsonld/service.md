# Service metadata

`{jsonld:service data='record'/}` maps a record to [Schema.org Service](https://schema.org/Service). Required name; description, service (serviceType), url, provider/providerurl (Organization), area (one or more named Places), price/currency and offerurl (Offer). A price requires currency. Supply areas as an array if a name contains a comma.

Only declared fields are exported. Existing URL filtering and script-safe JSON encoding apply. Schema vocabulary support does not guarantee search-engine rich-result eligibility. A complete record is in `apps/tags/examples/jsonld/service.php`. Run `php apps/regression/subsystems/jsonld-service.php`.
