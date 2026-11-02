# distributions: geometric

Counts failures before the first success: P(X=k)=(1-p)^k p, k≥0. Probability p is 1e-12..1; p=1 deterministically returns zero. Exact inverse transform uses log1p and checks integer overflow. No trial-count ambiguity; add one explicitly for trials.

Runnable example: `apps/tags/examples/distribution/geometric.pad`. Independent fixture: `php tests/subsystems/expansion-distributions-geometric.php`.
