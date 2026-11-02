# distributions: hypergeometric

Sequential sampling without replacement. Integer population, successes and draws are each 0..100000, with successes and draws no greater than population. Returns the number of successes. Empty population permits zero successes and draws. One uniform per draw, subject to the batch work cap. Exact small-case hypergeometric mass is covered by the fixture.

Runnable example: `apps/tags/examples/distribution/hypergeometric.pad`. Independent fixture: `php tests/subsystems/expansion-distributions-hypergeometric.php`.
