# statistics: correlation

Pearson correlation (default) or Spearman with average ranks for ties. Uses paired missing=omit/error, x/y field selectors, native finite values and shifted compensated centering. Constants or fewer than two pairs return status=degenerate and correlation=null. No p-values or causal claims. At most 100000 pairs.

Runnable example: `apps/tags/examples/stats/correlation.pad`. Independent fixture: `php tests/subsystems/expansion-statistics-correlation.php`.
