# statistics: bootstrap

Requires explicit seed, resamples (1..10000) and interval=percentile. Estimator is mean or median; confidence defaults .95. IID observations are resampled with replacement using a local deterministic SHA-256 counter stream and rejection to remove index modulo bias. No global RNG state changes. Type-7 quantiles form percentile bounds. maxWork defaults 2000000 and cannot exceed 10000000; sample/median sorting work and random draws are bounded. Does not claim automatic significance or support arbitrary callbacks.

Runnable example: `apps/tags/examples/stats/bootstrap.pad`. Independent fixture: `php tests/subsystems/expansion-statistics-bootstrap.php`.
