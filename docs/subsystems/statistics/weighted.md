# statistics: weighted

Weighted mean and sample variance with explicit semantics=frequency (integral counts) or reliability (nonnegative real weights). Defaults fields value/weight; paired missing=omit/error. Zero weights do not affect moments. Frequency denominator W−1; reliability denominator W−sum(w²)/W. Reports effectiveCount=W²/sum(w²), weightSum, positiveWeights and zeroWeights. Empty or zero-weight inputs yield null moments; insufficient denominator yields null variance. Uses shifted compensated accumulation with normalized weights.

Runnable example: `apps/tags/examples/stats/weighted.pad`. Independent fixture: `php tests/subsystems/expansion-statistics-weighted.php`.
