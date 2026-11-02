# Review and podcast metadata

`jsonld:review` emits [Review](https://schema.org/Review). Required inputs are name, body, item and author; the reviewed item is a Thing and the author a Person. Optional rating is a number from 1 through 5, the vocabulary's default rating bounds. Optional itemurl, published and url use shared URL/date validation. This is metadata, not an aggregate-rating calculator or a promise of search-engine eligibility.

`jsonld:podcast` emits [PodcastSeries](https://schema.org/PodcastSeries), with required name and url. Optional feed maps to webFeed, author accepts named people, publisher is an Organization, and description/image/language/start/end use the shared field pipeline. It does not claim to describe an individual PodcastEpisode.

Both preserve the existing field allowlist, escaped script serialization and required-field checks. Runnable tag examples show the emitted JSON. `php tests/subsystems/jsonld-review-podcast.php` validates nested types, rating bounds and script safety.
